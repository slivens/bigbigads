<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire {project}', function ($project) {
    $this->info("Ltest" . $project);
})->describe('Display an inspiring quote');


Artisan::command('bigbigads:change {email} {roleName}', function($email, $roleName) {
    try {
        $role = App\Role::where('name', $roleName)->first();
        if (!($role instanceof App\Role)) {
            $this->error("no such role");
            return;
        }
        $user = App\User::where('email', $email)->first();
        if ($user instanceof App\User) {
            $user->initUsageByRole($role);
            $user->save();
            $this->info("$email change to role:$roleName");
            /* $this->info($user->usage); */
        } else {
            $this->error("no such user:" . $email);
            return;
        }
    } catch(\Exception $e) {
        echo $e->getMessage();
    }
})->describe("设置用户的角色（将重新初始化资源使用情况）");

Artisan::command('bigbigads:activate {email} {state}', function($email,  $state) {
    try {
        $state = intval($state);
        $user = App\User::where('email', $email)->first();
        if ($user instanceof App\User) {
            $user->state = $state;
            $user->save();
            $this->info("$email is " . ($state == 1 ? "activated" : "anti-activated"));
            /* $this->info($user->usage); */
        } else {
            $this->error("no such user:" . $email);
            return;
        }
    } catch(\Exception $e) {
        echo $e->getMessage();
    }
})->describe("激活/反激活用户，state参数应为0或1");
