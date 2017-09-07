<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Log;
use Artisan;
use App\Jobs\LogAction;
use App\Role;
use Voyager;

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

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $captchaStr = Voyager::setting('captcha');
        $rules = [$this->username() => 'required', 'password' => 'required'];
        $messages = [];
        if ($captchaStr && strpos($captchaStr, 'login') !== FALSE) {
            $captchaType = Voyager::setting('captcha_type');
            if ($captchaType === 'recaptcha') {
                $rules['g-recaptcha-response'] = 'required|recaptcha';
                $messages['g-recaptcha-response.required'] = "validate you are not a robot";
            } else {
                $rules['captcha'] = 'required|captcha';
            }
        } 
        $this->validate($request, $rules, $messages);
    }

    protected function authenticated($request, $user)
    {
        //没审核通过或被冻结就不允许登陆
        if ($user->state == 0) {
            //临时新加需求，由于邮箱未送达率达到了近25%,暂时新加开关邮箱验证的功能，用户注册过后直接进入/app，对于state=0的用户
            //暂时不做任何的限制
            $emailVerification = Voyager::setting('email_verification');
            if ($emailVerification != "false") {
                Auth::logout();
                $this->redirectTo = "/sendVerifyMail?email={$user->email}";
            }           
			// Authentication passed...
        } else if ($user->state == 2) {
            Auth::logout();
            return view('auth.verify')->with('error', "Your account has freezed, please contact the Administrator!!!");
        }
        //帐号如果过期则重置为Free角色，这一步会比较耗时，是可以推到队列中执行，但是可能出现缓存与实际保存的不同步，由于只是偶尔执行，因此不会引起性能问题
        //简化处理
        /* if ($user->isExpired()) { */
            $user->resetIfExpired();
            /* $role = Role::where('name', 'Free')->first(); */
            /* $user->role_id = $role->id; */
            /* $user->expired = null; */
            /* $user->initUsageByRole($role); */
            /* $user->save(); */
			/* /1* Artisan::queue('bigbigads:change', [ *1/ */
			/* /1* 	'email' => $user->email, 'roleName' => 'Free' *1/ */
            /* /1* ]); *1/ */ 
            /* dispatch(new LogAction("USER_EXPIRED", json_encode(["name" => $user->name, "email" => $user->email, "expired" => $user->expired ]), "", $user->id, Request()->ip() )); */
        /* } */
        
        if ($request->has('referer')) {
            $url = env('APP_URL');
            if (strstr($request->referer, $url)) {
                return redirect($request->referer);
            }
        }
    }

    public function showLoginForm(Request $request)
    {
        if ($request->server('HTTP_REFERER') && $request->server('HTTP_REFERER') != env('APP_URL')) {
            return view('auth.login')->with('referer', $request->server('HTTP_REFERER'));
        } else {
            return view('auth.login');
        }
    }
}
