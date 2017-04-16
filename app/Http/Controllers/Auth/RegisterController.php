<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegisterVerify;
use Illuminate\Support\Facades\Auth;

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

    protected function registered($required, $user)
    {
        if ($user->state == 0) {
            Auth::logout();
            $this->redirectTo = "/sendVerifyMail?email={$user->email}&token={$user->verify_token}";
			// Authentication passed...
		}
    }
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role_id' => 3//不知为何没生效，默认设置为免费用户，下面再设置一遍
        ]);
        $user->role_id = 3;
        $user->verify_token = str_random(40);
        $user->save();
        /* Mail::to($user->email)->send(new RegisterVerify($user));//发送验证邮件 */
        return $user;
    }
}
