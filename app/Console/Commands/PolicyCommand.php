<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;

class PolicyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bba:policy {email? : 用户email} {key? : policy的键值} {value? : policy的值} {--remove : 删除该策略} {--rebuild : 重建用户的缓存}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '设置用户与Policy的关联';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function dump($user)
    {
        if ($user->getCachedPolicies()->count() == 0) {
            /* $this->info("{$user->email} has no user policies"); */
            return;
        }
        $this->info("{$user->email} user policies:");
        foreach ($user->getCachedPolicies() as $policy) {
            $this->comment("{$policy->key} : {$policy->pivot->value}");
        }
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = $this->argument('email');
        $key = $this->argument('key');
        $value = $this->argument('value');
        $remove = $this->option('remove');
        $rebuild = $this->option('rebuild');

        if (!$email && !$rebuild) {
            $this->error("email 不能为空");
            return;
        }
        if ($email) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $this->error("$email not found");
            }
        }

        // 重建User Policy缓存 
        if ($rebuild) {
            if (isset($user)) {
                $user->setCachePolicies();
                $this->dump($user);
            } else {
                foreach (User::cursor() as $user) {
                    $user->setCachePolicies();
                    $this->dump($user);
                }
            }
            return;
        }
        // 删除Policy
        if ($remove) {
            if (!$user->unsetPolicy($key)) {
                $this->error("unset $key failed");
                return;
            }
            $user->dumpUsage(function($msg) {
                $this->comment($msg);
            });
            return;
        }
        // 设置Policy
        if ($value) {
            if (!$user->setPolicy($key, $value)) {
                $this->error("set $key to $value failed");
                return;
            }
            $user->dumpUsage(function($msg) {
                $this->comment($msg);
            });
            return;
        } 
        if ($key) {
            // 查看Policy
            $policy  = $user->getPolicy($key);
            if (!$policy) {
                $this->error("{$email} has no set user policy:$key");
                return;
            }
            $this->comment("$key : {$policy->pivot->value}");
        } else {
            $this->dump($user);
        }
    }
}
