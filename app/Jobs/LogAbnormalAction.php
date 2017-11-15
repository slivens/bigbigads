<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\AbnormalActionLog;
use Auth;

class LogAbnormalAction implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    private $exceptionMessage;
    private $param;
    private $remark;
    private $uid;
    private $ip;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($exceptionMessage = null, $param, $remark = "", $uid = null, $ip = null)
    {
        $this->exceptionMessage = $exceptionMessage;
        $this->param = $param;
        $this->remark = $remark;
        if (is_null($this->remark))
            $this->remark = "";
        $this->uid = $uid;
        if (is_null($uid)) {
            if (!Auth::user())
                throw new \Exception("$type has no valid user id");
            $this->uid = Auth::user()->id;
        }
        $this->ip = $ip;
        if (!$ip)
            $this->ip = Request()->ip();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $action = new AbnormalActionLog();
        $action->user_id = $this->uid;
        $action->exception_message = $this->exceptionMessage;
        $action->ip = $this->ip;
        $action->param = $this->param;
        $action->remark = $this->remark;
        $action->save();
    }
}
