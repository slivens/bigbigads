<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use App\User;
use Voyager;
use TCG\Voyager\Models\Setting;
use Cache;

class SessionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bba:session {email? : 对指定的用户操作} {--session= : session数量超过指定值的用户} {--ips= : ip数量超过指定值的session} {--k|kick : 删除指定用户的session} {--reserve= : 保留指定用户session的数量} {--reserve-ip= : 限制单session的ip数量，与--save配合使用} {--start= : 只查找更新时间指定时间的会话或用户} {--save : 与--reserve, --reserve-ip配合，实时限制用户, --reserve,--reserve-ip值为0表示使用全局限制} {--cookie : 与-vv配合使用，指明是否显示cookie，可用于伪造测试} {--g|global : 配置全局参数}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = <<<EOF
在线用户会话管理。支持以下功能:
- 查看用户的session数量(-v),
- 查看每个session的ip及访问时间(-vv); 
- 过滤:只查看session数量或者ip数量满足条件的session，或者查看指定时间的session(--session,--ips,--start参数，如果指定了email，则过滤无效)
- 查看指定session的cookie，方便伪装用户(--cookie, -vv参数一起使用)
- 删除指定用户的session，可指定保留session数量(-k, 与--reserve配合)
- 限制所有用户单用户session数量，以及单session的IP数量(-g, --reserve, --reserve-ip配合)
- 限制指定用户的session数量，以及单session的IP数量(email参数， --save, --reserve, --reserve-ip配合)
EOF;

    private $service;
    private $encrypter;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(\App\Contracts\SessionService $service,  \Illuminate\Contracts\Encryption\Encrypter $encrypter)
    {
        parent::__construct();
        $this->service = $service;
        $this->encrypter = $encrypter;
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
        $reserveIp = $this->option('reserve-ip') ? : 0;
        $save = $this->option('save') ? : false;
        $cookie = $this->option('cookie') ? : false;
        $global = $this->option('global') ? : false;
        $start = $this->option('start') ? new Carbon($this->option('start')) : null;

        $service = $this->service;//app('app.service.session');
        if (!$email) {
            $sessionInfos = $service->sessionInfos();
            $userInfos = $service->userInfos();
            $this->info("total session count: " . count($sessionInfos));
            $this->info("total user count: " . count($userInfos));
            $this->info("limited session count per user(0 or empty means no limit): " . Voyager::setting('global_session_count'));
            $this->info("limited ip count per session(0 or empty means no limit): " . Voyager::setting('global_session_ip_count'));
        } else {
            $sessionInfos = $service->userSessions($email);
        }

        // -v 输出每个用户的session数量
        if ($this->output->isVerbose() && !$this->output->isVeryVerbose()) {
            $headers = ['email', 'session count', 'ip count'];
            $data = [];
            $sessionCount = 0;
            
            if ($email) {
                $sessionCount = count($sessionInfos);
                if ($sessionCount > 0) {
                    $headers[] = 'session limit/ip limit';
                    $user = User::where('email', $email)->first();
                    $ipCount = 0;
                    foreach ($sessionInfos as $session) {
                        $ipCount += count($session['ips']);
                    }
                    $data[] = [$email, $sessionCount, $ipCount, ($user->session_count ? : 'N') . '/' . ($user->session_ip_count ? : 'N')];
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
                    $data[] = [$email,  count($sessions), $ipCount, ''];
                    $sessionCount += count($sessions);
                }
            }
            $this->table($headers, $data);

            $this->info("summary: " . count($data) . " users, $sessionCount sessions");
        }
        // -vv 输出每个session的详细信息
        if ($this->output->isVeryVerbose()) {
            $headers = ['email', 'session', 'ip_count'];
            $data = [];

            if ($this->output->isDebug()) {
                $headers[] = 'ips';
            }
            if ($cookie) {
                $headers[] = "cookie";
            }


            foreach ($sessionInfos as $key => $session) {
                if (count($session['ips']) <= $minIpsCount)
                    continue;
                if ($start && (new Carbon($session['updated']))->lt($start))
                    continue;
                $row = [$session['email'], $key, count($session['ips'])];
                if ($this->output->isDebug()) {
                    $row[] = json_encode($session['ips']);
                }
                if ($cookie) {
                    $row[] = $this->encrypter->encrypt($key);
                }
                $data[] = $row;
            }
            $this->table($headers, $data);

            $this->info("summary: " . count($data) . "  sessions");
        }
        // -k
        if ($kick) {
            // 踢出操作完成后不再执行后续操作
            if (!$email && !$global) {
                $this->error("email is required, if you want to kick all, use -g");
                return;
            }

            if ($email) {
                $this->info("{$email} will be kicked, reserve {$reserve} sessions");
                $result = $service->removeUserSessions($email, $reserve);
                $this->info("left sessions : $result");
            } else {
                foreach ($userInfos as $email => $sessions) {
                    $result = $service->removeUserSessions($email, $reserve);
                    $this->info("$email is kicked, rserve {$reserve} sessions, left sessions: $result");
                }
            }
            
            return;
        }
        if ($save) {
            /* if ($reserve <= 0 && $reserveIp <= 0) { */
            /*     $this->error("--reserve, --reserve-ip should bigger than 0"); */
            /*     return; */
            /* } */
            $user = User::where('email', $email)->first();
            if (!$user)
                $this->error("$email not found");
            $user->session_count = $reserve > 0 ? $reserve : null;
            $user->session_ip_count = $reserveIp > 0 ? $reserveIp : null;
            $user->save();
            $this->info("$email session setting saved: session_count " . ($user->session_count ? : 'N') . ", session_ip_count " . ($user->session_ip_count ? : 'N'));
        }
        if ($global) {
            if ($this->option('reserve') !== null) {
                Setting::where('key', 'global_session_count')
                    ->update(['value' => $this->option('reserve')]);
                $this->info("Global Session Limit update");
            }
            if ($this->option('reserve-ip') !== null) {
                // 与实际业务产生了耦合，业务逻辑的代码都挪到Service中会更好
                Setting::where('key', 'global_session_ip_count')
                    ->update(['value' => $this->option('reserve-ip')]);
                Cache::forget('global_session_ip_count');
                $this->info("Global Session IP Limit update");
            }
        }
    }
}
