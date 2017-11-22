<?php

namespace App\Http\Middleware;

use Closure;
use Cache;
use Voyager;
use Auth;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class ControlSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $res = $next($request);
        $limitIpCount = Cache::remember('global_session_ip_count', 86400, function() {
            return Voyager::setting('global_session_ip_count');
        });
        if (!$limitIpCount || $limitIpCount < 1)
            return $res;
        $user = Auth::user();
        if ($user) {
            if ($user->session_ip_count && $user->session_ip_count > 0)
                $limitIpCount = $user->session_ip_count;

            $statics = $request->session()->get('session_statics');
            if (count($statics['ips']) > $limitIpCount) {
                // 加锁防止重复删除同一个session并生成多个session
                // 不用删除锁，自然过期即可
                // 正常而言，session已经被重新生成了，所以后续不会再有相同的session来请求加锁
                // 如果有，那一定是在重新生成之前就进来的请求，而这些请求本来就应该被忽略
                // 所以如果删除锁，这些请求将再次创建锁并再次生成session，是没有必要的。
                // 因此直接让锁自动过期即可
                $ok = Redis::set($request->session()->getId() . '.lock', 1, 'EX','30', 'NX');
                if ($ok) {
                    $statics['ips'] = [];
                    $statics['ips'][$request->ip()] = Carbon::now()->toIso8601String();
                    $request->session()->set('session_statics', $statics);
                    $request->session()->migrate(true);
                }
            }
        }

        return $res;
    }
}
