<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class SessionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bba:session {email? : 对指定的用户操作} {--session= : session数量超过指定值的用户} {--ips= : ip数量超过指定值的session} {--k|kick : 删除指定用户的session} {--reserve= : 保留指定用户session的数量} {--start= : 只查找更新时间指定时间的会话或用户}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '在线用户会话管理。加-v查看用户的session数量,加-vv查看每个session的ip及访问时间; 如果指定了email, --session与--ips参数无效。';

    private $service;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(\App\Contracts\SessionService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $minSessionCount = $this->option('session') ? : 0;
        $minIpsCount = $this->option('ips') ? : 0;
        $email = $this->argument('email');
        $kick = $this->option('kick');
        $reserve = $this->option('reserve') ? : 0;
        $start = $this->option('start') ? new Carbon($this->option('start')) : null;

        $service = $this->service;//app('app.service.session');
        $sessionInfos = $service->sessionInfos();
        $userInfos = $service->userInfos();
        $this->info("total session count: " . count($sessionInfos));
        $this->info("total user count: " . count($userInfos));

        // -v 输出每个用户的session数量
        if ($this->output->isVerbose() && !$this->output->isVeryVerbose()) {
            $headers = ['email', 'session count', 'ip count'];
            $data = [];
            $sessionCount = 0;

            if ($email) {
                if (isset($userInfos[$email])) {
                    $ipCount = 0;
                    foreach ($userInfos[$email] as $session) {
                        $ipCount += count($session['ips']);
                    }
                    $data[] = [$email, count($userInfos[$email] ? : []), $ipCount];
                }
            } else {
                foreach ($userInfos as $email => $sessions) {
                    if (count($sessions) <= $minSessionCount)
                        continue;
                    if ($start) {
                        // 只要有一个session满足条件，就认为该email处于活动状态
                        $filtered = Arr::where($sessions, function($val) use ($start) {
                            return (new Carbon($val['updated']))->gte($start);
                        });
                        if (count($filtered) == 0)
                            continue;
                    }
                    $ipCount = 0;
                    foreach ($sessions as $session) {
                        $ipCount += count($session['ips']);
                    }
                    if ($ipCount <= $minIpsCount)
                        continue;
                    $data[] = [$email,  count($sessions), $ipCount];
                    $sessionCount += count($sessions);
                }
            }
            $this->table($headers, $data);

            $this->info("summary: " . count($data) . " users, $sessionCount sessions");
        }
        // -vv 输出每个session的详细信息
        if ($this->output->isVeryVerbose()) {
            $headers = ['email', 'session', 'ips', 'ip_count'];
            $data = [];
            if ($email) {
                $infos = $userInfos[$email] ? : [];
            } else {
                $infos = $sessionInfos;
            }

            foreach ($infos as $key => $session) {
                if (count($session['ips']) <= $minIpsCount) 
                    continue;
                if ($start && (new Carbon($session['updated']))->lt($start)) 
                    continue;
                $data[] = [$session['email'], $key, json_encode($session['ips']), count($session['ips'])];
            }
            $this->table($headers, $data);

            $this->info("summary: " . count($data) . "  sessions");
        }
        // -k
        if ($kick) {
            $this->info("{$email} will be kicked, reserve {$reserve} sessions");
            $result = $service->removeUserSessions($email, $reserve);
            $this->info("left sessions : $result");
        }      
    }
}
