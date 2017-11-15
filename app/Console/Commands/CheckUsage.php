<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Role;
use App\User;

class CheckUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bba:check-usage {email?} {--fix-user : 修复用户usage} {--fix-role : 修复角色缓存usage} {--fix : 修复用户usage和角色缓存usage} {--all : 检查所有用户}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '检查usage是否符合预期设计;默认只检查角色，需要通过参数以检查用户usage';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = $this->argument('email');
        $fixUser = $this->option('fix-user');
        $fixRole = $this->option('fix-role');
        $verbose = $this->option('verbose');
        $fix = $this->option('fix');
        $all = $this->option('all');
        if ($fix) {
            $fixUser = true;
            $fixRole = true;
        }

        $this->comment("checking role cache usage...");
        foreach (Role::all() as $role) {
            try {
                $role->checkCacheUsage();
                if ($verbose && !$email) {
                    $this->info("Role {$role->name} usage detail:");
                    $role->dumpUsage(function ($msg) {
                        $this->comment($msg);
                    });
                }
            } catch(\App\Exceptions\GenericException $e) {
                $this->error($e->getMessage());
                if ($fixRole) {
                    $role->generateCache();
                }
            }
        }


        if ($email) {
            $this->comment("checking $email 's usage...");
            $user = User::where('email', $email)->first();
            if (!$user) {
                $this->error("$email not found");
                return;
            }

            try {
                $user->checkUsage();
            } catch(\App\Exceptions\GenericException $e) {
                $this->error($e->getMessage());
                if ($fixUser) {
                    $this->comment("try fix it");
                    $user->reInitUsage();
                }
            }
            if ($verbose)
                $user->dumpUsage(function ($msg) {
                    $this->comment($msg);
                });
        } else {
            if (!$all)
                return;

            $this->comment("checking users's usage...");
            $users = User::paginate(5000)->toArray();
            for ($paginate = 1; $paginate <= $users['last_page']; $paginate++) {
                foreach (User::paginate(5000, ['*'], '', $paginate) as $user) {
                    try {
                        $user->checkUsage();
                    } catch(\App\Exceptions\GenericException $e) {
                        $this->error($e->getMessage());
                        if ($fixUser) {
                            $this->comment("try fix it");
                            $user->reInitUsage();
                        }
                    }
                    if ($verbose)
                        $user->dumpUsage(function ($msg) {
                            $this->comment($msg);
                        });
                }
            }
        }

    }
}
