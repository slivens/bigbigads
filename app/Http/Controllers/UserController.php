<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Role;
use App\User;
use Carbon\Carbon;
use Log;
use App\Services\AnonymousUser;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendRegistMail;

class UserController extends Controller
{
    use ResetsPasswords;

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
     * 用户邮箱注册验证
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
     * 未验证登陆跳到此页面，然后提示
     */
    public function noverify(Request $request)
    {
        if (!($request->has('email') && $request->has('token'))) {
            return view('auth.verify')->with('error', "parameter error");
        }
        $user = User::where('email', $request->email)->where('verify_token', $request->token)->first();
        if (($user instanceof User) && $user->state == 1) {
            return view('auth.verify')->with('error', "You have verified, don't verify again!!!");
        }
        $link = "/sendVerifyMail?email={$request->email}&token={$request->token}";
        return view('auth.verify')->with("error", "Your email {$request->email} has not verified")->with("link", $link);
    }

    public function sendVerifyMail(Request $request) {
        if (!($request->has('email') && $request->has('token'))) {
            return view('auth.verify')->with('error', "parameter error");
        }
        $user = User::where('email', $request->email)->where('verify_token', $request->token)->first();
        if (($user instanceof User) && $user->state == 1) {
            return view('auth.verify')->with('error', "You have verified, don't verify again!!!");
        }
        dispatch(new SendRegistMail($user));//Mail::to($user->email)->queue(new RegisterVerify($user));//发送验证邮件
        return view('auth.verify')->with('info', "Your email {$request->email} has sent, please check your email. ");
    }
}
