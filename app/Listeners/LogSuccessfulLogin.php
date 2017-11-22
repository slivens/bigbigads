<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\LogAction;
use App\Jobs\SessionControlJob;
use Jenssegers\Agent\Agent;
use App\ActionLog;
use Voyager;
use Log;
use Carbon\Carbon;

/**
 * 目前完成两件事：
 *
 * - 添加登陆审计事件
 * - 自动控制Session数量
 */
class LogSuccessfulLogin 
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  SomeEvent  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;
        $agent = new Agent();
        if ($agent->isMobile() || $agent->isTablet()) {
            dispatch(new LogAction(ActionLog::ACTION_USER_LOGIN_MOBILE, json_encode(["name" => $user->name, "email" => $user->email]), "", $user->id, Request()->ip() ));
        } else {
            dispatch(new LogAction(ActionLog::ACTION_USER_LOGIN, json_encode(["name" => $user->name, "email" => $user->email]), "", $user->id, Request()->ip() ));
        }
        // Session可能在一个完整的Request->Response完成时写入，而event推入队列后是同步执行的
        // 如果要精确控制Session数量，应该延迟几秒执行以保证效果
        dispatch((new SessionControlJob($user))->delay(Carbon::now()->addSeconds(5)));
    }
}
