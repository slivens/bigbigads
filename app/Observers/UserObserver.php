<?php

namespace App\Observers;

use Illuminate\Foundation\Auth\User;
use App\Affiliate;

class UserObserver
{
    /**
     * 监听用户创建的事件。
     *
     * @param  User  $user
     * @return void
     */
    public function created(User $user)
    {
        // 创建用户时同时创建推荐码
        $aff = Affiliate::create([
            'name'      => $user->name,
            'email'     => $user->email,
            'password'  => $user->password,
            'track'     => str_random(10),
            'status'    => 1,
            'type'      => 0
        ]);

        $user->aff_id = $aff->id;
        $user->save();
    }

    /**
     * 监听用户更新事件。
     *
     * @param  User  $user
     * @return void
     */
    public function updated(User $user)
    {
        // 更新用户时同时更新关联推荐码的字段
        $aff = Affiliate::find($user->aff_id);

        $aff->name = $user->name;
        $aff->email = $user->email;
        $aff->password = $user->password;

        $aff->save();
    }

    /**
     * 监听用户删除事件。
     *
     * @param  User  $user
     * @return void
     */
    public function deleting(User $user)
    {
        // 删除用户时同时删除关联推荐码
        $aff = Affiliate::find($user->aff_id);
        $aff->delete();
    }
}