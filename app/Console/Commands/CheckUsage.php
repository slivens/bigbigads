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
    protected $signature = 'bba:check-usage {email?} {--fix-user : 修复用户usage} {--fix-role : 修复角色缓存usage} {--fix : 修复用户usage和角色缓存usage}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '检查权限是否符合预期设计';

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
        if ($fix) {
            $fixUser = true;
            $fixRole = true;
        }

        $this->comment("checking role cache usage...");
        foreach (Role::all() as $role) {
            try {
                $role->checkCacheUsage();
            } catch(\App\Exceptions\GenericException $e) {
                $this->error($e->getMessage());
                if ($fixRole) {
                    $role->generateCache();
                }
            }
        }

        $this->comment("checking users's usage...");

        if ($email) {
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
            foreach (User::cursor() as $user) {
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
