<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Role;
use App\User;
use App\Payment;
use App\CustomizedInvoice;
use Carbon\Carbon;
use App\Services\AnonymousUser;
use App\Jobs\SendRegistMail;
use App\Jobs\GenerateInvoiceJob;
use Log;
use Socialite;
use Validator;
use App\Jobs\LogAction;
use Illuminate\Auth\Events\Registered;
use App\Contracts\PaymentService;
use App\Jobs\ResendRegistMail;
use GuzzleHttp;
use Jenssegers\Agent\Agent;
use App\ActionLog;

use App\AppRegistersUsers;
use App\Jobs\SendVerifyCodeMail;

class UserController extends Controller
{
    use ResetsPasswords;
    use AppRegistersUsers;

    protected $socialiteProviders = ['github', 'facebook', 'linkedin', 'google'];
    private $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    /**
     * 更改密码
     */
    public function changepwd(Request $req)
    {
        $this->validate(
            $req, [
            'newpwd' => 'required|min:8|max:32',
            'repeatpwd' => 'required|min:8|max:32'
            ]
        );
        $user = Auth::user();
        /* return ['code'=>-1, 'desc' =>$user->email . ":" . $req->oldpwd]; */
        if (!Auth::attempt(['email' => $user->email, 'password' => $req->oldpwd]))
            return ['code' => -1, 'desc' => trans('auth.failed')];
        if ($req->newpwd != $req->repeatpwd) {
            return ['code' => -1, 'desc' => trans('auth.failed_repeat')];
        }
        $this->resetPassword($user, $req->newpwd);
        return ['code' => 0, 'desc' => 'success'];
    }

    /**
     * 修改用户名&邮箱
     * 需要做检测，尤其邮箱需要
     * 20171113 前端暂时去除email修改，后端方法先留着
     * 20171116 修改方法，入参修改，只传输要修改的，email处理删除，后续再加
     * 
     * @param Request $req post请求上来的数据，改了什么传上来什么。
     * @return response
     * 
     * @todo 邮箱验证有现成方法，延后验证，要是没通过如何？回退的话，之前的邮箱存在什么地方
     */
    public function changeProfile(Request $req)
    {
        $user = Auth::user();
        if ($req->name) {
            $rules = [
                'name' => 'required|max:64'
            ];
            $validator = Validator::make($req->all(), $rules);
            if ($validator->fails()) {
                $res = ['code' => -1, 'desc' => trans('profile.name_too_long')];
            } else {
                $req->name == $user->name ? $res = ['code' => -1, 'desc' => trans('messages.not_changed')] : $user->name = $req->name;
            }
        } else {
            $res = ['code' => -1, 'desc' => trans('messages.not_empty')];// 字段不能为空/或者传上来其他字段，不处理直接当空返回
        }
        if (!isset($res)) {
            if ($user->save()) {
                $res = ['code' => 0, 'desc' => trans('messages.save_done')]; // 修改成功
            } else {
                $res = ['code' => -1, 'desc' => trans('messages.save_failed')]; // 修改失败
            }
        }
        return response()->json($res);
    }

    /**
     * 返回登陆用户信息
     */
    public function logInfo(Request $req)
    {
        // 返回登陆用户的session信息，包含
        // 用户基本信息、权限信息
        $res = [];
        $user = Auth::user();
        if ($user) {
            // TODO:Role的permission与policies必须从缓存中读取
            $user->load('role', 'role.permissions', 'role.policies', 'subscriptions', 'subscriptions.payments');
            $res['login'] = true;
            $res['user'] = $user;
            // 将购买的相关计划也要返回，必须缓存，这一步很慢
            if ($user['subscription_id'] != null) {
                $user->load('subscription');//有订阅就把订阅信息也一起加载
            }
            //add by chenxin 20171114,修复了Issue #37
            $res['failed_recurring_payments'] = $this->paymentService->onFailedRecurringPayments($user->subscriptions);
            $res['effective_sub'] = $user->getEffectiveSub()?true:false;
            $res['permissions'] = $user->getMergedPermissions()->groupBy('key');
            $res['groupPermissions'] = $user->getMergedPermissions()->groupBy('table_name');
            // 提供用户邮箱的hamc，用于intercom的用户验证
            $res['emailHmac'] = hash_hmac(
                'sha256',
                $user->email,
                'sh7oS9Q3bk0m-Vs3pEeUjCOMKGjoyjf1bVcTfCiY'
            );
            // 可能存在affiliate不存在的情况
            if ($affiliate = $user->affiliates()->first()) {
                // track/click/action三个数据只从第一条affiliate记录取（暂行）
                $res['user']['affiliateUrl'] = env('APP_URL') . '?track=' . $affiliate->track;
                $res['user']['click'] = $affiliate->click;
                $res['user']['action'] = $affiliate->action;
            } else {
                $res['user']['affiliateUrl'] = false;
                $res['user']['click'] = 0;
                $res['user']['action'] = 0;
            }
            $userRetryTime = 'retryTime_'.$user->id;
            $res['user']['retryTime'] = Cache::get($userRetryTime);
        } else {
            $user = AnonymousUser::user($req);
            $res['login'] = false;
            $res['user'] = $user;
            $res['permissions'] = $user->role->permissions->groupBy('key');
            $res['groupPermissions'] = $user->role->permissions->groupBy('table_name');
        }
        return json_encode($res, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 用户进入邮箱后点开注册验证的链接完成验证
     */
    public function registerVerify(Request $request)
    {
        if (!($request->has('email') && $request->has('token'))) {
            return view('auth.verify')->with('error', "parameter error");
        }
        $user = User::where('email', $request->email)->where('verify_token', $request->token)->first();
        if (!($user instanceof User)) {
            return view('auth.verify')->with('error', "Verify failed");
        }
        if ($user->state == 1) {
            return view('auth.verify')->with('error', "You have verified, don't verify again!!!");
        }
        $user->state = 1;
        $user->save();
        Auth::login($user);
        $agent = new Agent();
        if ($agent->isMobile()) {
            return redirect('/mobile');
        } else {
            return redirect('/app');
        }
        // return view('auth.verify')->with("user", $user);
    }


    /**
     * 发送验证邮件
     */
    public function sendVerifyMail(Request $request)
    {
        if (!($request->has('email'))) {
            return view('auth.verify')->with('error', "parameter error");
        }
        $user = User::where('email', $request->email)->first();
        if (($user instanceof User) && $user->state == 1) {
            return view('auth.verify')->with('error', "You have verified, don't verify again!!!");
        }
        $this->registerDispatch($user);
        $info = "Your email {$request->email} has sent, please check your email.";
        $email = $request->email;
        return view('auth.verify', compact('info', 'email'));
    }

    /**
     * 社交登陆重定向
     * 支持:
     * 1. Github
     * 2. Facebook
     * 3. LinkedIn
     * 4. Google+
     */
    public function socialiteRedirect(Request $request, $name)
    {
        if (!in_array($name, $this->socialiteProviders)) {
            return view('auth.verify')->with('error', "unsupported provider:$name");
        }
        $socialite = Socialite::driver($name);
        if ($request->has('track')) {
            $request->session()->set('track', $request->track);
        }
        return $socialite->redirect();
    }

    /**
     * (原始需求)
     * 社交帐号登陆成功后：
     * 1. 如果原来没有帐号就要求设置密码创建新帐号
     * 2. 如果原来已有帐号但要求输入密码完成绑定
     * 3. 否则就是直接完成登陆跳到主页面
     * (现有需求)[已实现]
     * 社交帐号登陆成功后:
     * 1. 如果原来没有帐号，如果有email生成对应帐户，否则根据唯一id生成<id>@bigbigads.com的帐户
     * 2. 如果原来有帐号就直接绑定
     * 3. 跳转到主页面
     */
    public function socialiteCallback(Request $request, $name)
    {
        if (!in_array($name, $this->socialiteProviders)) {
            return view('auth.verify')->with('error', "unsupported provider:$name");
        }
        try {  
            $socialiteUser = Socialite::driver($name)->user();
        } catch(\Exception $e) {
            return view('auth.verify')->with('error', "$name login encounter some errors, please login again");
        }
        $email = $socialiteUser->email;
        $token = $socialiteUser->token;
        $providerId = $socialiteUser->id;
        /* if (empty($email)) { */
        /* return $this->message("sorry, no email information in your '$name' account"); */
        /* } */
        Log::debug("oauth:" . json_encode($socialiteUser));
        Log::debug("request:" . json_encode(request()->all()));
        return $this->autoBind($request, $name, $socialiteUser);
        /* return redirect()->action('UserController@bindForm', ['name' => $name, 'token' => $token, 'email' => $email]); */
    }


    /**
     * 根据新需求完成自动绑定
     * 
     * @param Request Request $request 
     * @param String          $name    name
     * 
     * @ref socialiteCallback
     */
    protected function autoBind(Request $request, $name, $socialiteUser)
    {
        $binded = false;
        $email = $socialiteUser->email;
        $providerId = $socialiteUser->id;
        $edm = 1;
        if (empty($email)) {
            $email = $socialiteUser->id . '@bigbigads.com';
            $edm = 0;
        }
        // 没有帐号就先创建匿名帐号
        $user = User::where('email', $email)->first();
        if (!$user instanceof User) {
            $userName = $socialiteUser->nickname;
            if (empty($userName)) {
                $userName= $socialiteUser->name;
            }
            if (empty($userName)) {
                $userName = $email;
            }
            $user = User::create(
                [
                'name' => $userName,
                'email' => $email,
                'password' => bcrypt(str_random(10)),
                'role_id' => 3
                ]
            );
            $user->state = 1;//社交帐号直接通过验证
            /* $user->role_id = 3; */
            $user->edm = 0;
            $user->verify_token = str_random(40);
            $user->regip = $request->ip();
            $track = $request->session()->pull('track', null);
            if ($track) {
                $affiliate = \App\Affiliate::where(['track' => $track, 'status' => 1, 'type' => 1])->first();
                if ($affiliate) {
                    $user->affiliate_id = $affiliate->id;
                    $affiliate->action++;
                    $affiliate->save();
                }
            }
            $user->save();
            event(new Registered($user));
        }
        // 在有帐号的情况下，完成自动绑定并登陆
        if (\App\Socialite::where(['provider_id' => $providerId, 'provider' => $name])->count() > 0) {
            $binded = true;
        }
        if (!$binded) {
            $item =  new \App\Socialite();
            $item->provider_id = $providerId;
            $item->provider = $name;
            $item->bind = $email;
            $item->remark = json_encode($socialiteUser);
            $item->save();
            $agent = new Agent();
            if ($agent->isMobile() || $agent->isTablet()) {
                dispatch(new LogAction(ActionLog::ACTION_USER_BIND_SOCIALITE_MOBILE_BASE . strtoupper($name), json_encode(["name" => $user->name, "email" => $user->email]), $name, $user->id, Request()->ip()));
            } else {
                dispatch(new LogAction(ActionLog::ACTION_USER_BIND_SOCIALITE_BASE . strtoupper($name), json_encode(["name" => $user->name, "email" => $user->email]), $name, $user->id, Request()->ip()));
            }
            // 社交登录请求转化代码页面，需求变更，弃用，改为跳转至注册欢迎页面
            /* $domain = env('APP_URL');
            $url = $domain . 'socialiteStat.html?query=socialte_' . $name;
            $client = new GuzzleHttp\Client();
            $res = $client->requestAsync('GET', $url);*/
            Auth::login($user);
            return redirect('welcome#?socialite=' . $name);
        } 
        Auth::login($user);
        $agent = new Agent();
        if ($agent->isMobile()) {
            return redirect('/mobile');
        } else {
            return redirect('/app/#');
        }
    }    

    /**
     * 绑定已有用户的表单
     * 
     * @deprecated 需求变更，抛弃
     */
    public function bindForm(Request $request, $name)
    {
        return view('auth.bind')->with('name', $name)->with('token', $request->token)->with('email', $request->email);
    }

    /**
     * 提交绑定后的处理
     * 
     * @deprecated 需求变更，抛弃
     */
    public function bind(Request $request, $name)
    {
        $token = $request->token;
        $socialiteUser = Cache::get($token, null);
        if ($socialiteUser== null) {
            return view('auth.verify')->with('error', "the page is expired");
        }
        $email = $socialiteUser->email;
        $providerId = $socialiteUser->id;
        // 没有帐号就先创建帐号
        $user = User::where('email', $email)->first();
        if (!$user instanceof User) {
            $rules = [
                'password' => 'required|min:6'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return back()->with('status', 'password requires at least 6 characters');
            }

            $userName = $socialiteUser->nickname;
            if (empty($userName)) {
                $userName= $socialiteUser->name;
            }
            $user = User::create(
                [
                'name' => $userName,
                'email' => $email,
                'password' => bcrypt($request->password),
                'role_id' => 3
                ]
            );
            $user->state = 1;// 社交帐号直接通过验证
            /* $user->role_id = 3; */
            $user->verify_token = str_random(40);
            $user->save();
            event(new Registered($user));
        }

        if (!Auth::attempt(['email' => $email, 'password' => $request->password])) {
            return back()->with('status', 'wrong password');
        }

        // 有帐号就检查是否绑定，已经绑定就直接登陆
        $binded = false;
        if (\App\Socialite::where(['provider_id' => $providerId, 'provider' => $name])->count() > 0) {
            $binded = true;
        }
        if (!$binded) {
            $item =  new \App\Socialite();
            $item->provider_id = $providerId;
            $item->provider = $name;
            $item->bind = $email;
            $item->remark = json_encode($socialiteUser);
            $item->save();
        }
        Auth::login($user);
        dispatch(new LogAction("USER_BIND_SOCIALITE", json_encode(["name" => $user->name, "email" => $user->email]), $name, $user->id, Request()->ip()));
        $agent = new Agent();
        if ($agent->isMobile()) {
            return redirect('/mobile');
        } else {
            return redirect('/app/#');
        }
    }

    public function quickRegister(Request $request)
    {
        // 新需求，新增快捷注册，页面异步请求，用户无需填写密码，由前端页面生成
        $validator = Validator::make(
            $request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
            ]
        );
        if ($validator->fails()) {
            return ['code' => -1, 'desc' => $validator->messages()];
        }
        // create 接受的是数组
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['password'] = $request->password; 
        if ($request->track) {
            $data['track'] = $request->track;
        }
        $user = $this->create($data);
        event(new Registered($user));
        $this->registerDispatch($user);
        Auth::login($user);
        // 返回成功信息通信前端注册成功
        return ['code' => 0, 'desc' => 'register success'];
    
    }

    public function registerDispatch($user)
    {
        dispatch(new SendRegistMail($user));// Mail::to($user->email)->queue(new RegisterVerify($user));//发送验证邮件
        // 用户注册完后往队列加入一个2分钟延迟的任务，检测是否送达用户邮箱，否则的话使用gmail再重发一次
        $twoMinutesDelayJob = (new ResendRegistMail($user, 'delivered', 2))->delay(Carbon::now()->addMinutes(2));
        dispatch($twoMinutesDelayJob);
    }

    public function recordContinue(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            dispatch(new LogAction(ActionLog::ACTION_RECORD_CLICK_CONTINUE, $user->email, 'record_click_continue', $user->id, $request->ip()));
        }
    }

    public function filterLogRecord(Request $request)
    {
        $user = Auth::user();
        if ($user->id != $request->userId) {
            return ['code' => -1, 'desc' => 'Unauthorised User'];
        }
        dispatch(new LogAction(ActionLog::ACTION_USER_REQUEST_FILTER, json_encode($request->params), '', $user->id, $request->ip()));
    }

    /**
     * 获取用户票据自定义信息
     * 只获取表单显示的部分内容
     *
     * @return Response
     */
    public function getInvoiceCustomer()
    {
        $user = Auth::user();
        $customer = CustomizedInvoice::select('company_name', 'address', 'contact_info', 'website', 'tax_no')->where('user_id', $user->id)->first();
        return response()->json($customer);
    }

    
    /**
     * 存储提交上来的定制信息
     * 新创建的用户，没有交易订单但是可以存储，存储完毕后给出提示信息，没有票据生成操作
     * 已经付款的用户，有交易订单，已经有生成过票据，存储完毕后重新生成票据，每个自然月操作1次
     * 只有保存且有订单的情况下才生成票据
     *
     * @return Response
     *
     * @todo 需要优化写法
     */
    public function setInvoiceCustomer(Request $request)
    {
        $user = Auth::user();
        $extraData = [
            'company_name' => $request->company_name,
            'address' => $request->address,
            'contact_info' => $request->contact_info,
            'website' => $request->website,
            'tax_no' => $request->tax_no
        ];
        $extraData = self::filterEmojiDeep($extraData); // 删掉emoji
        if ($custom = CustomizedInvoice::where('user_id', $user->id)->first()) {
            if ($custom->canSave()) {
                $modifiedCustom = CustomizedInvoice::updateOrCreate(
                    [
                        'user_id' => $user->id
                    ],
                    $extraData
                );
                if ($modifiedCustom != $custom) {
                    if (count($user->payments) > 0) {
                        $res = [
                            'code' => 0,
                            'desc' => trans('profile.re_generate')
                        ];
                        //推入队列执行,有修改才执行
                        dispatch((new GenerateInvoiceJob(Payment::where('client_id', $user->id)->where('status', Payment::STATE_COMPLETED)->get(), true, $extraData)));
                    } else {
                        $res = [
                            'code' => 0,
                            'desc' => trans('profile.need_to_payed')
                        ];
                    }
                } else {
                    $res = [
                            'code' => 0,
                            'desc' => trans('messages.not_changed')
                    ];
                }
            } else {
                $res = [
                    'code' => -1,
                    'desc' => trans('profile.change_limit')
                ];
            }
        } else {
            CustomizedInvoice::updateOrCreate(
                [
                    'user_id' => $user->id
                ],
                $extraData
            );
            if (count($user->payments) > 0) {
                $res = [
                    'code' => 0,
                    'desc' => trans('profile.re_generate')
                ];
                //推入队列执行,有修改才执行
                dispatch((new GenerateInvoiceJob(Payment::where('client_id', $user->id)->where('status', Payment::STATE_COMPLETED)->get(), true, $extraData)));
            } else {
                $res = [
                    'code' => 0,
                    'desc' => trans('profile.need_to_payed')
                ];
            }
        }
        return response()->json($res);
    }

    /**
     * 发送激活邮件到用户提交的email
     *
     * @return Response
     */

    public function sendVerifyMailToSubEmail(Request $request)
    {
        $user = Auth::user();
        $email = $request->user_email;

        $validator = Validator::make($request->only('subscription_email'), [
                'subscription_email' => 'required|email|max:255|unique:users,subscription_email,'.$user->id,
            ]
        );

        if ($validator->fails())
        {
            return $this->responseError($validator->messages(), -1);
        }

        $user->subscription_email = $request->subscription_email;
        $user->save();

        $userRetryTime = 'retryTime_'.$user->id;
        if (null === Cache::get($userRetryTime)) {
            Cache::put($userRetryTime, 3, Carbon::tomorrow());
        }
        $retryTime = Cache::get($userRetryTime);
        if ($retryTime && $retryTime > 0) {
            Cache::forget($userRetryTime);
            // 覆盖无效, 删除再创建
            Cache::put($userRetryTime, $retryTime - 1, Carbon::tomorrow());
            dispatch(new sendVerifyCodeMail($user));
            return response()->json(['code' => 1, 'time' => Cache::get($userRetryTime)]);
        } else {
            return response()->json(['code' => -401, 'desc' => 'Run out of retry time for resend email']);
        }
    }

    /**
     * 验证订阅邮箱
     *
     * @return Response
     */

    public function subEmailVerify(Request $request)
    {
        // 无subEmail 和 token 抛出异常
        if (!($request->has('subEmail') && $request->has('token'))) {
            return view('auth.verify')->with('error', "parameter error");
        }

        $user = User::where('subscription_email', $request->subEmail)->first();
        if (!($user instanceof User)) {
            return view('auth.verify')->with('error', "Verify failed");
        }

        // 社交登录用户检查验证 || 桌面端邮箱登录用户检查验证
        if ($user->is_check == 1) {
            return view('auth.verify')->with('error', "You have verified, don't verify again!!!");
        }

        $userCode = 'verifyCode_'.$user->id;
        $verifyCode = Cache::get($userCode);

        // token码有效性检查，不存在即过期
        if (!$verifyCode) {
            return view('auth.verify')->with('error', "The verification email has expired!!!");
        }

        if ($verifyCode != $request->token) {
            return view('auth.verify')->with('error', "parameter error");
        }

        $user->is_check = 1;
        $user->state = 1;
        $user->save();
        Auth::login($user);
        return redirect('/app');
    }

    /*
     * 替换掉emoji表情，并且删除头尾的空格
     * @param $text 待处理字符串
     * @param string $replaceTo 替换成
     * @return mixed|string
     */
    public static function filterEmoji($text, $replaceTo = '')
    {
        $clean_text = "";
        // Match Emoticons
        $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clean_text = preg_replace($regexEmoticons, $replaceTo, $text);
        // Match Miscellaneous Symbols and Pictographs
        $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clean_text = preg_replace($regexSymbols, $replaceTo, $clean_text);
        // Match Transport And Map Symbols
        $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clean_text = preg_replace($regexTransport, $replaceTo, $clean_text);
        // Match Miscellaneous Symbols
        $regexMisc = '/[\x{2600}-\x{26FF}]/u';
        $clean_text = preg_replace($regexMisc, $replaceTo, $clean_text);
        // Match Dingbats
        $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
        $clean_text = preg_replace($regexDingbats, $replaceTo, $clean_text);
        return trim($clean_text);
    }

    /**
     * 替换掉数组中的emoji表情
     * @param $arrayString 待处理字符串/字符串组
     * @param string $replaceTo 替换到
     * @return mixed|string
     */
    public static function filterEmojiDeep($arrayString, $replaceTo = '')
    {
        if (is_string($arrayString)) {
            return self::filterEmoji($arrayString, $replaceTo);
        } elseif (is_array($arrayString)) {
            foreach ($arrayString as &$array) {
                if (is_array($array) || is_string($array)) {
                    $array = self::filterEmojiDeep($array, $replaceTo);
                } else {
                    $array = $array;
                }
            }
        }
        return $arrayString;
    }
}
