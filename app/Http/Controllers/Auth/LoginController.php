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
use Response;
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
        if ($agent->isMobile()) {
            return redirect('/mobile');
        }

        //没审核通过或被冻结就不允许登陆
        if ($user->state == 0) {
            //临时新加需求，由于邮箱未送达率达到了近25%,暂时新加开关邮箱验证的功能，用户注册过后直接进入/app，对于state=0的用户
            //暂时不做任何的限制
            $emailVerification = Voyager::setting('email_verification');
            if ($emailVerification != "false") {
                Auth::logout();
                if ($request->expectsJson()) {
                    return Response::json(['redirectTo' => "/sendVerifyMail?email={$user->email}"], 422);
                }
                $this->redirectTo = "/sendVerifyMail?email={$user->email}";
            }           
			// Authentication passed...
        } else if ($user->state == 2) {
            Auth::logout();
            $messages = 'Your account was temporarily banned. Please check your mail-box or contact help@bigbigads.com for more info.';
            if ($request->expectsJson()) {
                return Response::json(['redirectTo' => '/error', 'message' => $messages], 422);
            }
            return view('auth.verify')->with('error', $messages);
        }
        //简化处理
        $user->resetIfExpired();
        
        if ($request->has('referer')) {
            $url = env('APP_URL');
            if (strstr($request->referer, $url)) {
                if ($request->expectsJson()) {
                    return Response::json(['redirectTo' => $request->referer]);
                }
                return redirect($request->referer);
            }
        }
        if ($request->expectsJson()) {
            return Response::json(['redirectTo' => '/app']);
        }
    }

    public function showLoginForm(Request $request)
    {
        $httpReferer = $request->server('HTTP_REFERER');
        if ($httpReferer && $httpReferer != env('APP_URL') && !$this->ignorePath($httpReferer)) {
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

        if ($request->expectsJson()) {
            return Response::json(['redirect' => '/']);
        }
        return redirect('/');
    }

    /**
     * 记住未登录时的访问页面排除网站自身的静态页面
     * Todo: 当有新的静态页面新加时需要添加
     */
    public function ignorePath($path)
    {
        $isWebPath = false;
        $webPath = [
            'privacy_policy',
            'terms_service',
            'about',
            'plan',
            'pricing',
            'product',
            'post',
            'blog',
        ];
        foreach($webPath as $key) {
            if (strpos($path, $key)) {
                $isWebPath = true;
            }
        }
        return $isWebPath;
    }
}
