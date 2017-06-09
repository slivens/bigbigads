<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegisterVerify;
use Swift_SmtpTransport;
use Swift_Mailer;
use Carbon\Carbon;
// 若是像SendRegistMail那样引入会报错：Call to undefined method Bogardo\Mailgun\Facades\Mailgun::get()
use Mailgun\Mailgun;
use Log;
class ResendRegistMail implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $user;
    protected $action;
    protected $time;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $action, $time)
    {
        $this->user = $user;
        $this->action = $action;
        $this->time = $time;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = $this->user->email;
        $action = $this->action;
        $time = $this->time;
        /*
            正常邮箱激活流程
            Accepted -- > Delivered ---> Opened -->Clicked
        */
        if ($this->user->state != 1) {
            $mailGunKey = env('MAILGUN_SECRET');
            $mailGunClient = new Mailgun($mailGunKey);
            $domain = env('MAILGUN_DOMAIN');
            $isAction = false;
            // 查询时间格式为 'Fri, 3 May 2013 09:00:00 -0000'
            $queryTime = Carbon::now()->subMinute($time)->toRfc2822String();
            //$queryTime = 'Fri, 3 May 2013 09:00:00 -0000';
            $queryString = array(
                'begin'        => $queryTime,
                'ascending'    => 'yes',
                'limit'        =>  25,
                'pretty'       => 'yes',
                'recipient'    => $email
            );

            # Make the call to the client.
            $result = $mailGunClient->get("$domain/events", $queryString);
            //可能出现查询不到该邮件的记录，可能是出错或者是由gmail发送的
            if (count($result->http_response_body->items) > 0) {
                foreach ($result->http_response_body->items as $item) {
                    if ($item->event == $action) {
                        $isAction = true;
                    }
                }
                if (!$isAction) {
                    //第一次查询没有Delivered状态，第二次查询没有Opened的状态下使用gmail重发
                    Log::debug("<$email> resend by gmail because of no $action " . Carbon::now());
                    Mail::to($email)->send(new RegisterVerify($this->user));
                } 
            } else {
                Log::debug("mailgun query $email is no result " . Carbon::now());
            }
        }
    }
}
