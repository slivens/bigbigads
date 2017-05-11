<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Carbon\Carbon;
use Log;
use Artisan;
use App\Jobs\LogAction;
use App\Role;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/app';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    protected function authenticated($request, $user)
    {
        //没审核通过或被冻结就不允许登陆
        if ($user->state == 0) {
            Auth::logout();
            $this->redirectTo = "/sendVerifyMail?email={$user->email}";
			// Authentication passed...
        } else if ($user->state == 2) {
            Auth::logout();
            return view('auth.verify')->with('error', "Your account has freezed, please contact the Administrator!!!");
        }
        //帐号如果过期则重置为Free角色，这一步会比较耗时，是可以推到队列中执行，但是可能出现缓存与实际保存的不同步，由于只是偶尔执行，因此不会引起性能问题
        //简化处理
        if ($user->role_id != 3 && $user->expired  && $user->expired != '0000-00-00 00:00:00' && Carbon::now()->gt(new Carbon($user->expired))) {
            $role = Role::where('name', 'Free')->first();
            $user->role_id = $role->id;
            $user->expired = null;
            $user->initUsageByRole($role);
            $user->save();
			/* Artisan::queue('bigbigads:change', [ */
			/* 	'email' => $user->email, 'roleName' => 'Free' */
            /* ]); */ 
            dispatch(new LogAction("USER_EXPIRED", json_encode(["name" => $user->name, "email" => $user->email, "expired" => $user->expired ]), "", $user->id, Request()->ip() ));
        }
    }
}
