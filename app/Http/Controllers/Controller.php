<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\AnonymousUser;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 获取当前用户
     */
    public function user()
    {
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = AnonymousUser::user(request());
        }
        return $user;
    }

    /**
     * 目前错误的返回统一以422作为Response返回码
     */
    public function responseError($desc, $code = -1) 
    {
        return response(["code"=>$code, "desc"=> $desc], 422);
    }
}
