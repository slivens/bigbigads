<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Voyager;
use Log;

class SessionControlJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    private $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->user;
        // Session可能在一个完整的Request->Response完成时写入，而event推入队列后是同步执行的
        // 如果要精确控制Session数量，应该延迟半秒以上执行才能保证效果
        // 检查Session数量，如果全局global_session_count为0或者无设置，则认为不限制。-1表示不限制
        /* Log::debug("delay"); */
        $globalSessionCount = intval(Voyager::setting('global_session_count') ? : -1); 
        if ($globalSessionCount == 0)
            $globalSessionCount = -1;
        $sessionService = app('app.service.session');        

        $userSessions = $sessionService->userSessions($user->email);
        if (count($userSessions) == 0)
            return;
        $left = $sessionService->removeUserSessions($user->email, $user->session_count !== null ? $user->session_count : $globalSessionCount);
        if ($left < 0)
            return;
        if (count($userSessions) != $left) {
            Log::info("limit {$user->email} session count:", ['from' => count($userSessions), 'to' => $left]);
        }
    }
}
