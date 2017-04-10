<?php

use Illuminate\Foundation\Inspiring;
use App\Services\AnonymousUser;

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
            $user->role_id = $role->id;
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
    $stateDesc = ['anti-activated', 'activated', 'freezed'];
    try {
        $state = intval($state);
        $user = App\User::where('email', $email)->first();
        if ($user instanceof App\User) {
            $user->state = $state;
            $user->save();
            $this->info("$email is " . $stateDesc[$state]);
            /* $this->info($user->usage); */
        } else {
            $this->error("no such user:" . $email);
            return;
        }
    } catch(\Exception $e) {
        echo $e->getMessage();
    }
})->describe("激活/反激活/冻结用户，state参数应为0,1,2。0表示待激活,1表示激活,2表示冻结");

class MockReq {
    public function ip() {
        return '192.168.1.200';
    }
}

Artisan::command('bigbigads:can {email} {priv}', function($email,  $priv) {
    try {
        $user = App\User::where('email', $email)->first();
        if ($user instanceof App\User) {
            if ($user->can($priv)) {
                $this->info("$email has $priv ability");
            } else {
                $this->error("$email has no $priv ability");
            }
            $usage = $user->getUsage($priv);
            if (!$usage)
                return;
            $this->info("usage info:" . json_encode($usage));
        } else {
            $this->error("no such user:" . $email . ", show anonymous USER");

            $user = AnonymousUser::user(new MockReq());
            if ($user->can($priv)) {
                $this->info(" has $priv ability");
            } else {
                $this->error(" has no $priv ability");
            }
        }
    } catch(\Exception $e) {

        echo $e->getMessage();
    }
})->describe("检查用户权限");

