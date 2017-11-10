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

use Jenssegers\Agent\Agent;
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
        $agent = new Agent();
        if ($agent->isMobile()) return redirect('/mobile');

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
            return view('auth.verify')->with('error', "Your account was temporarily banned. Please check your mail-box or contact help@bigbigads.com for more info.");
        }
        //简化处理
        $user->resetIfExpired();
        
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

    /**
     * 默认的logout，没有删除session即重新生成，导致session大量累计
     * 无法准确地判断在线用户。此处改写为regenerate时先删除上一个session。
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->flush();

        $request->session()->regenerate(true);

        return redirect('/');
    }

}
