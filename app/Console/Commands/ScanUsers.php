<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\User;

class ScanUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bba:scan-users {email?} {--fix-info : 根据订阅信息修复用户的角色和过期时间}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '扫描所有用户或者指定用户，如果已经过期，就重置权限';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function check($user)
    {
        if (!$user->expired || $user->expired == '0000-00-00 00:00:00') {
            return;
        }
        $expired = new Carbon($user->expired);
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fix = $this->option('fix-info');
        $email = $this->argument('email');
        if ($fix) {
            $this->comment("You enabled --fix-info option");
        }
        if ($email) {
            $user = User::where('email', $email)->first();
            if ($fix) {
                $user->fixInfoByPayments();
            }
            $user->resetIfExpired();
            $this->comment("only scan user: $email ". $user->isExpired());
        } else {
            $this->comment("start scan users...");
            foreach (User::where('role_id', '>', 2)->cursor() as $user) {
                if ($fix) {
                    $user->fixInfoByPayments();
                }
                // 免费用户忽略 
                if ($user->role_id == 3)
                    continue;
                if ($user->resetIfExpired())
                    $this->info("{$user->email} has expired");
            }
        }
    }
}
