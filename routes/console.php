<?php

use Illuminate\Foundation\Inspiring;
use App\Services\AnonymousUser;
use App\Maillist;
use App\ActionLog;
use App\Jobs\LogAction;
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

Artisan::command('bba:resetpwd {email}', function($email) {
    $user = App\User::where('email', $email)->first();
    if ($user instanceof App\User) {
        $user->password = bcrypt('bigbigads');
        $user->save();
        $this->info("$email reset password to 'bigbigads'");
        /* $this->info($user->usage); */
    } else {
        $this->error("no such user:" . $email);
        return;
    }
})->describe("重置密码为bigbigads");

Artisan::command('bba:change {email} {roleName}', function($email, $roleName) {
    try {
        $role = App\Role::where('name', $roleName)->first();
        if (!($role instanceof App\Role)) {
            $this->error("no such role");
            return;
        }
        $user = App\User::where('email', $email)->first();
        if ($user instanceof App\User) {
            $oldrole = $user->role;
            $user->role()->associate($role);
            $user->reInitUsage();
            $user->save();
            $this->info("$email change to role:$roleName");

            dispatch(new LogAction(ActionLog::ACTION_ROLE_MANUAL_CHANGE, json_encode($user->makeHidden('usage')), 'old role:' . json_encode($oldrole), -1, '0.0.0.0'));
            /* $this->info($user->usage); */
        } else {
            $this->error("no such user:" . $email);
            return;
        }
    } catch(\Exception $e) {
        echo $e->getMessage();
    }
})->describe("设置用户的角色（将重新初始化资源使用情况）");

Artisan::command('bba:activate {email} {state}', function($email,  $state) {
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

Artisan::command('bba:can {email} {priv}', function($email,  $priv) {
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

/**
 * 邮件相关的操作，目前支持:
 * add 批量添加: php artisan bba:email add <mail file name>
 * del 批量删除: php artisan bba:email del <mail file name>
 * clear 全部删除: php artisan bba:email clear
 * dispatch 发送邮件测试: php artisan bba:email dispatch m13799329269@qq.com weekly
 * 格式: email,group
 * 其中，group不应该以'#'开头，'#'开头的分组有特殊含义
 */
Artisan::command('bba:email {op : 子命令,add批量添加,del批量删除,dispatch邮件发送测试} {file=mail : 文件路径} {extParam=space : 额外的参数，比如邮件测试需要指定模板}', function($op,  $file, $extParam) {
    if ($op == 'add' || $op == 'del') {
        if (!Storage::exists($file)) {
            $this->info("$file is not found, please check the file is in 'storage/app'");
            return;
        }
        $content = Storage::get($file);
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $item = explode(",", $line);
            if (count($item) < 2)
                break;
            if ($op == 'add') {
                Maillist::firstOrCreate([
                    'email' => $item[0], 
                    'category' => $item[1]
                ]);
                $this->info("{$item[1]}: {$item[0]} added");
            } else {
                Maillist::where([
                    'email' => $item[0], 
                    'category' => $item[1]
                ])->delete();
                $this->info("{$item[1]}: {$item[0]} deleted");
            }
        }
    } else if ($op == 'clear') {
        Maillist::where('id', '>', 0)->delete();
        $this->info("the maillist is cleared");
    } else if ($op == 'dispatch') {
        //对于dispatch来说，$extParam是mail.edm的键值
        $edmMails = config('mail.edm');
        if (!array_key_exists($extParam, $edmMails)) {
            $this->error("$extParam config is not in mail.edm");
            return;
        }
        $mailConfig =$edmMails[$extParam];
        $className = $mailConfig['class'];
        $email = $file;
        if (!class_exists($className)) {
            $this->error("$className is not found");
            return;
        }
        /* Mail::to($email)->send(new $className($email)); */
        app('app.service.user')->sendMail($email, new $className($email));
        $this->info("send to $email successfully");
    }
})->describe("从文件中批量添加/删除邮件，以及测试邮件的发送(测试邮件阻塞直到发送出去方便测试)");
