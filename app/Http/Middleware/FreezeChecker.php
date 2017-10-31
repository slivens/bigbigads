<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;

class FreezeChecker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if (($user instanceof User) && ($user->state  == User::STATE_FREEZED)) {
            // TODO:必须优化返回码，统一规定
            return response(["code"=> -5000, "desc"=> trans('auth.freezed')], 422);
        }
        return $next($request);
    }
}
