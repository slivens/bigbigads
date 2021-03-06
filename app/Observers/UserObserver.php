<?php

namespace App\Observers;

use Illuminate\Foundation\Auth\User;
use App\Affiliate;
use App\Role;
use Log;

class UserObserver
{
    /**
     * 监听用户创建事件。
     *
     * @param  User  $user
     * @return void
     */
    public function created(User $user)
    {
        // 创建用户时同时创建推荐码
        $affiliate = Affiliate::create([
            'name'      => $user->name,
            'email'     => $user->email,
            'password'  => $user->password,
            'track'     => str_random(10),
            'status'    => 1,
            'type'      => 1
        ]);
        $role = $user->role;
        if (!$role)
            $role = Role::find($user->role_id);
        if (!$role) {
            Log::warning("role not found for {$user->email}, role id:{$user->role_id}");
            return;
        }
        $user->reInitUsage($role);
    }

    /**
     * 监听用户更新事件。
     *
     * @param  User  $user
     * @return void
     */
    public function updating(User $newUser)
    {
        $oldUser = User::find($newUser->id);

        if ($oldUser->email != $newUser->email) {
            // 更新用户时同时更新关联推荐码的字段
            Affiliate::where('email', $oldUser->email)->update([
                'email' => $newUser->email
            ]);
        }
    }

    /**
     * 监听用户删除事件。
     *
     * @param  User  $user
     * @return void
     */
    // public function deleting(User $user)
    // {
    //     // 删除用户时同时删除关联推荐码
    //     Affiliate::where('email', $user->email)->delete();
    // }
}
