<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegisterVerify;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Jobs\SendRegistMail;
use Voyager;

use App\AppRegistersUsers;
use App\Jobs\LogAction;
use App\ActionLog;
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;
    use AppRegistersUsers;

    /**
     * Bigbigads的login与register是同一个页面，重定向
     */
    public function showRegistrationForm()
    {
        return view('auth.login');
    }

    /**
     * Where to redirect users after login / registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            //暂时去掉确认密码验证
            //'password' => 'required|min:6|confirmed',
            'password' => 'required|min:6',
        ]);
    }

    protected function registered($request, $user)
    {
        //临时新加需求,由于邮箱未送达率达到了近25%,暂时新加开关邮箱验证的功能，用户注册过后直接进入/app，对于state=0的用户
        //暂时不做任何的限制
        //后续需求会给出对这次放过的未进行邮箱验证的用户处理方式
        //暂定注册流程 注册 -> 欢迎界面

        // 对属于BBA系统测试的邮箱，不发送邮件，只记录log
        if (self::checkIsBBAEmail($user->email)) {
            dispatch(new LogAction(ActionLog::ACTION_REGISTERED_BY_BIGBIGADSTEST, $user->email, "registred_by_bigbigadstest", $user->id, $request->ip()));
        } else {
            dispatch(new SendRegistMail($user));
        }
        return redirect('welcome?socialite=email');
        /* 需求变更，暂时抛弃，以后流程会再更改，加入新用户引导的部分
        $emailVerification = Voyager::setting('email_verification');
        if ($emailVerification != "false") {
            if ($user->state == 0) {
                 Auth::logout();
                 $this->redirectTo = "/sendVerifyMail?email={$user->email}";
                 // Authentication passed...
                 if ($request->ajax()) {
                     return ['code' => 0, 'url' => $this->redirectTo];
                 }
             }
        } else {
            //照样发送激活邮件，绕过提示点击邮件激活的页面，为以后区别出恶意注册的用户
            //更改需求，跳转至欢迎页面
            dispatch(new SendRegistMail($user));
            return redirect('welcome?socialte=email');
        }*/  
    }
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    //移到AppRegistersUsers中 
    /*protected function create(array $data)
    {   
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role_id' => 3, //不知为何没生效，默认设置为免费用户，下面再设置一遍
        ]);*/
        /* $user->role_id = 3; */
        /*$user->verify_token = str_random(40);
        $user->regip = request()->ip();
        if (array_key_exists('track', $data)) {
            $affiliate = \App\Affiliate::where(['track' => $data['track'], 'status' => 1, 'type' => 1])->first();
            if ($affiliate) {
                $user->affiliate_id = $affiliate->id;
                $affiliate->action++;
                $affiliate->save();
            }
        }
        $user->save();*/
        /* Mail::to($user->email)->send(new RegisterVerify($user));//发送验证邮件 */
     /*   return $user;
    }*/

    protected function checkIsBBAEmail($email)
    {
        if (strstr($email, '@bigbigadstest.com')) {
            return true;
        }
        return false;
    }
}
