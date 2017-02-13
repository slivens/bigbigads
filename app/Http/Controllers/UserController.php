<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Role;
use Carbon\Carbon;
use Log;
use App\Services\AnonymousUser;

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
            //将购买的相关计划也要返回
            if ($user->hasBraintreeId()) {
                $res['subscription'] = $user->subscriptions->first();
            }
            $res['permissions'] = $user->role->permissions->groupBy('key');
            $res['groupPermissions'] = $user->role->permissions->groupBy('table_name');
        } else {
            $user = AnonymousUser::user($req);
            $res['login'] = false;
            $res['user'] = $user;
            $res['permissions'] = $user->role->permissions->groupBy('key');
            $res['groupPermissions'] = $user->role->permissions->groupBy('table_name');
        }
        return json_encode($res, JSON_UNESCAPED_UNICODE);
    }

}