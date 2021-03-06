<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Mailgun;
use App\Mail\VerifyCodeMail;
use Swift_SmtpTransport;
use Swift_Mailer;

class SendVerifyCodeMail implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $user;
    protected $forceDefault;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $forceDefault = false)
    {
        $this->user = $user;
        $this->forceDefault = $forceDefault;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->user)
            return;
        // 发送到用户的订阅邮件
        $email = $this->user->subscription_email;
        // $useMailgun = false;
        // if (!empty(env('MAILGUN_USERNAME'))) {
        //     $useMailgun = true;
        // }
        // if ($useMailgun && !$this->forceDefault) {
        //     $verifyCodeMail = new VerifyCodeMail($this->user);
        //     Mailgun::send($verifyCodeEmail->viewName, $verifyCodeMail->params(), function($message) use($email) {
        //         $message->to($email)->subject("Bigbigads:Please verify your email address")->tag(['registerVerify']);// todo tag的作用
        //     });
        // } else {
        //     Mail::to($email)->send(new VerifyCodeMail($this->user));
        // }
        $res = app('app.service.user')->sendMail($email, new VerifyCodeMail($this->user), [
            'tags' => [
                'registerVerify'
            ]
        ]);
    }
}
