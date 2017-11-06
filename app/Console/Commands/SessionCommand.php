<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SessionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bba:session {email? : 对指定的用户操作} {--session : session数量超过指定值的用户} {--ips : ip数量超过指定值的session} {k? : 删除指定用户的session}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '在线用户会话管理';

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
        $service = app('app.service.session');
        $sessions = $service->sessionInfos();
        $users = $service->userInfos();
        $this->info("sessions count: " . count($sessions));
        $this->info("users count: " . count($users));
    }
}
