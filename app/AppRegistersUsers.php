<?php
namespace App;

use App\User;

trait AppRegistersUsers
{
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
            'role_id' => 3, //不知为何没生效，默认设置为免费用户，下面再设置一遍
        ]);
        /* $user->role_id = 3; */
        $user->verify_token = str_random(40);
        $user->regip = request()->ip();
        if (array_key_exists('track', $data)) {
            $affiliate = \App\Affiliate::where(['track' => $data['track'], 'status' => 1, 'type' => 1])->first();
            if ($affiliate) {
                $user->affiliate_id = $affiliate->id;
                $affiliate->action++;
                $affiliate->save();
            }
        }
        $user->save();
        /* Mail::to($user->email)->send(new RegisterVerify($user));//发送验证邮件 */
        return $user;
    }
}