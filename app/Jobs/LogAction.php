<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\ActionLog;

class LogAction implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    private $type;
    private $param;
    private $remark;
    private $uid;
    private $ip;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($type, $param, $remark, $uid, $ip = null)
    {
        $this->type = $type;
        $this->param = $param;
        $this->remark = $remark;
        $this->uid = $uid;
        $this->ip = $ip;
        if (!$ip)
            $this->ip = Request()->ip();
    }

    /**
     * 队列没有上下文信息，$uid与$ip必须自定义
     *
     * @return void
     */
    public function handle()
    {
        $action = new ActionLog();
        $action->user_id = $this->uid;
        $action->type = $this->type;
        $action->ip = $this->ip;
        $action->param = $this->param;
        $action->remark = $this->remark;
        $action->save();
    }
}
