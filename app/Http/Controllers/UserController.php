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
        return view('auth.verify')->with('info', "Your email {$request->email} has sent, please check your email. ");
    }

    /**
     * 社交登陆重定向
     * 支持:
     * 1. Github
     * 2. Facebook
     * 3. LinkedIn
     * 4. Google+
     */
    public function socialiteRedirect($name)
    {
        if (!in_array($name, $this->socialiteProviders)) {
            return view('auth.verify')->with('error', "unsupported provider:$name");
        }
        return Socialite::driver($name)->redirect();
    }

    /**
     * 社交帐号登陆成功后：
     * 1. 如果原来没有帐号就要求设置密码创建新帐号
     * 2. 如果原来已有帐号但要求输入密码完成绑定
     * 3. 否则就是直接完成登陆跳到主页面
     * 如果完成绑定一定会在\App\Socialite有记录，所以检查该表即可
     */
    public function socialiteCallback($name)
    {
        if (!in_array($name, $this->socialiteProviders)) {
            return view('auth.verify')->with('error', "unsupported provider:$name");
        }
        
        $socialiteUser = Socialite::driver('github')->user();
        $email = $socialiteUser->email;
        $token = $socialiteUser->token;
        Cache::put($token, $socialiteUser, 5 * 60);
        if (\App\Socialite::where(['email' => $email, 'provider' => $name])->count() > 0) {
            $user = User::where('email', $email)->first();
            Auth::login($user);
            /* dispatch(new LogAction("USER_LOGIN", json_encode(["name" => $user->name, "email" => $user->email]), $name , $user->id, Request()->ip() )); */
            return redirect('/app');
           }
        return redirect()->action('UserController@bindForm', ['name' => $name, 'token' => $token, 'email' => $email]);
    }

    /**
     * 绑定已有用户的表单
     */
    public function bindForm(Request $request, $name)
    {
        return view('auth.bind')->with('name', $name)->with('token', $request->token)->with('email', $request->email);
    }

    /**
     * 社交帐号登陆成功后：
     * 1. 如果原来没有帐号就要求设置密码创建新帐号
     * 2. 如果原来已有帐号但要求输入密码完成绑定
     * 3. 否则就是直接完成登陆跳到主页面
     */
    public function bind(Request $request, $name)
    {
        $token = $request->token;
        $socialiteUser = Cache::get($token, null);
        if ($socialiteUser== null) {
            return view('auth.verify')->with('error', "the page is expired");
        }
        $email = $socialiteUser->email;
        //没有帐号就创建帐号
        $user = User::where('email', $email)->first();
        if ($user instanceof User) {
            Log::debug("info:$email, {$request->password}");
            if (!Auth::attempt(['email' => $email, 'password' => $request->password])) {
                return back()->with('status', 'wrong password');
            }
             
            //有帐号就检查是否绑定，已经绑定就直接登陆
            $binded = false;
            if (\App\Socialite::where(['email' => $email, 'provider' => $name])->count() > 0)
                $binded = true;
            if (!$binded) {
                $item =  new \App\Socialite();
                $item->provider = $name;
                $item->email = $email;
                $item->bind = $email;
                $item->save();
            }
            Auth::login($user);
            dispatch(new LogAction("USER_BIND_SOCIALITE", json_encode(["name" => $user->name, "email" => $user->email]), $name , $user->id, Request()->ip() ));
            return redirect('/app');
        }

        $rules = [
            'password' => 'required|min:6'
        ];
		$validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->with('status', 'password requires at least 6 characters');
        }
  
        $user = User::create([
            'name' => $socialiteUser->nickname,
            'email' => $email,
            'password' => bcrypt($request->password),
        ]);
        $user->state = 1;//社交帐号直接通过验证
        $user->role_id = 3;
        $user->verify_token = str_random(40);
        $user->save();
        event(new Registered($user));
        Auth::login($user);
        dispatch(new LogAction("USER_BIND_SOCIALITE", json_encode(["name" => $user->name, "email" => $user->email]), $name , $user->id, Request()->ip() ));
        return redirect('/app');
    }

}
