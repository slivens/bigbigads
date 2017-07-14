<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Role;
use App\User;
use Carbon\Carbon;
use App\Services\AnonymousUser;
use App\Jobs\SendRegistMail;
use Log;
use Socialite;
use Validator;
use App\Jobs\LogAction;
use Illuminate\Auth\Events\Registered;

use App\Jobs\ResendRegistMail;
use GuzzleHttp;
use BrowserDetect;
class UserController extends Controller
{
    use ResetsPasswords;

    protected $socialiteProviders = ['github', 'facebook', 'linkedin', 'google'];

    /**
     * 更改密码
     */
    public function changepwd(Request $req) {
        $this->validate($req, [
            'newpwd' => 'required|min:8|max:32',
            'repeatpwd' => 'required|min:8|max:32'
        ]);
        $user = Auth::user();
        /* return ['code'=>-1, 'desc' =>$user->email . ":" . $req->oldpwd]; */
        if (!Auth::attempt(['email' => $user->email, 'password' => $req->oldpwd]))
            return ['code' => -1, 'desc' => 'password is wrong'];
        if ($req->newpwd != $req->repeatpwd) {
            return ['code' => -1, 'desc' => 'new password and repeat password is not the same'];
        }
        $this->resetPassword($user, $req->newpwd);
        return ['code' => 0, 'desc' => 'success'];
    }

    /**
     * 返回登陆用户信息
     */
    public function logInfo(Request $req)
    {
        //返回登陆用户的session信息，包含
        //用户基本信息、权限信息
        $res = [];
        $user = Auth::user();
        if ($user) {
            $user->load('role', 'role.permissions', 'role.policies');
            $res['login'] = true;
            $res['user'] = $user;
            //将购买的相关计划也要返回，必须缓存，这一步很慢
            if ($user['subscription_id'] != null) {
                $user->load('subscription');//有订阅就把订阅信息也一起加载
            }
            $res['permissions'] = $user->getMergedPermissions()->groupBy('key');
            $res['groupPermissions'] = $user->getMergedPermissions()->groupBy('table_name');
            //提供用户邮箱的hamc，用于intercom的用户验证
            $res['emailHmac'] = hash_hmac(
                                    'sha256',
                                    $user->email,
                                    'sh7oS9Q3bk0m-Vs3pEeUjCOMKGjoyjf1bVcTfCiY'
                                );
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
        return redirect("/app");
        //return view('auth.verify')->with("user", $user);
    }


    /**
     * 发送验证邮件
     */
    public function sendVerifyMail(Request $request) {
        if (!($request->has('email'))) {
            return view('auth.verify')->with('error', "parameter error");
        }
        $user = User::where('email', $request->email)->first();
        if (($user instanceof User) && $user->state == 1) {
            return view('auth.verify')->with('error', "You have verified, don't verify again!!!");
        }
        dispatch(new SendRegistMail($user));//Mail::to($user->email)->queue(new RegisterVerify($user));//发送验证邮件
        //用户注册完后往队列加入一个2分钟延迟的任务，检测是否送达用户邮箱，否则的话使用gmail再重发一次
        $twoMinutesDelayJob = (new ResendRegistMail($user, 'delivered', 2))->delay(Carbon::now()->addMinutes(2));
        dispatch($twoMinutesDelayJob);
        $info = "Your email {$request->email} has sent, please check your email.";
        $email = $request->email;
        return view('auth.verify',compact('info','email'));
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
        if ($request->has('track'))
            $socialite->with(['state' => $request->track]);
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
    public function socialiteCallback($name)
    {
        if (!in_array($name, $this->socialiteProviders)) {
            return view('auth.verify')->with('error', "unsupported provider:$name");
        }
        
        $socialiteUser = Socialite::driver($name)->stateless()->user();
        $email = $socialiteUser->email;
        $token = $socialiteUser->token;
        $providerId = $socialiteUser->id;
        /* if (empty($email)) { */
        /*     return $this->message("sorry, no email information in your '$name' account"); */
        /* } */
        Log::debug("oauth:" . json_encode($socialiteUser));
        Log::debug("request:" . json_encode(request()->all()));
        return $this->autoBind(request(), $name, $socialiteUser);
        /* return redirect()->action('UserController@bindForm', ['name' => $name, 'token' => $token, 'email' => $email]); */
    }


    /**
     * 根据新需求完成自动绑定
     * @var Request $request 
     * @var String $name
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
        //没有帐号就先创建匿名帐号
        $user = User::where('email', $email)->first();
        if (!$user instanceof User) {
            $userName = $socialiteUser->nickname;
            if (empty($userName))
                $userName= $socialiteUser->name;

            $user = User::create([
                'name' => $userName,
                'email' => $email,
                'password' => bcrypt(str_random(10))
            ]);
            $user->state = 1;//社交帐号直接通过验证
            $user->role_id = 3;
            $user->edm = 0;
            $user->verify_token = str_random(40);
            $user->regip = $request->ip();
            if ($request->has('state')) {
                $affiliate = \App\Affiliate::where(['track' => $request->state, 'status' => 1, 'type' => 1])->first();
                if ($affiliate) {
                    $user->affiliate_id = $affiliate->id;
                    $affiliate->action++;
                    $affiliate->save();
                }
            }
            $user->save();
            event(new Registered($user));
        }
        //在有帐号的情况下，完成自动绑定并登陆
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
            if (BrowserDetect::isMobile()) {
                dispatch(new LogAction("USER_BIND_SOCIALITE_MOBILE_" . strtoupper($name), json_encode(["name" => $user->name, "email" => $user->email]), $name , $user->id, Request()->ip() ));
            } else {
                dispatch(new LogAction("USER_BIND_SOCIALITE_" . strtoupper($name), json_encode(["name" => $user->name, "email" => $user->email]), $name , $user->id, Request()->ip() ));
            }
            //社交登录请求转化代码页面，需求变更，弃用，改为跳转至注册欢迎页面
            /*$domain = env('APP_URL');
            $url = $domain . 'socialiteStat.html?query=socialte_' . $name;
            $client = new GuzzleHttp\Client();
            $res = $client->requestAsync('GET', $url);*/
            Auth::login($user);
            return redirect('welcome?socialite=' . $name);
        } 
        Auth::login($user);
        return redirect('/app/#');
    }    

    /**
     * 绑定已有用户的表单
     * @deprecated 需求变更，抛弃
     */
    public function bindForm(Request $request, $name)
    {
        return view('auth.bind')->with('name', $name)->with('token', $request->token)->with('email', $request->email);
    }

    /**
     * 提交绑定后的处理
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
        //没有帐号就先创建帐号
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
            if (empty($userName))
                $userName= $socialiteUser->name;
            $user = User::create([
                'name' => $userName,
                'email' => $email,
                'password' => bcrypt($request->password),
            ]);
            $user->state = 1;//社交帐号直接通过验证
            $user->role_id = 3;
            $user->verify_token = str_random(40);
            $user->save();
            event(new Registered($user));
        }

        if (!Auth::attempt(['email' => $email, 'password' => $request->password])) {
            return back()->with('status', 'wrong password');
        }

        //有帐号就检查是否绑定，已经绑定就直接登陆
        $binded = false;
        if (\App\Socialite::where(['provider_id' => $providerId, 'provider' => $name])->count() > 0)
            $binded = true;
        if (!$binded) {
            $item =  new \App\Socialite();
            $item->provider_id = $providerId;
            $item->provider = $name;
            $item->bind = $email;
            $item->remark = json_encode($socialiteUser);
            $item->save();
        }
        Auth::login($user);
        dispatch(new LogAction("USER_BIND_SOCIALITE", json_encode(["name" => $user->name, "email" => $user->email]), $name , $user->id, Request()->ip() ));
        return redirect('/app/#');
    }

}
