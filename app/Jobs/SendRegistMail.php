<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Mailgun;
use App\Mail\RegisterVerify;
use Swift_SmtpTransport;
use Swift_Mailer;

class SendRegistMail implements ShouldQueue
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
        $email = $this->user->email;
        $useMailgun = false;
        /* $useMail2 = false; */
        //暂不启用
        /* if (preg_match('/qq\.com$/', $email)) { */
        /*     $useMail2 = true; */
        /* } */
        /* if ($useMail2) { */
        /*     $backup = Mail::getSwiftMailer(); */
        /*     $config = config('mail.addons.mail2'); */

        /*     $transport = Swift_SmtpTransport::newInstance($config['host'], $config['port'], $config['encryption']); */
        /*     $transport->setUsername($config['username']); */
        /*     $transport->setPassword($config['password']); */
        /*     $mail2 = new Swift_Mailer($transport); */
        /*     Mail::setSwiftMailer($mail2); */
        /* } */

        if (!empty(env('MAILGUN_USERNAME'))) {
            $useMailgun = true;
        }
        if ($useMailgun && !$this->forceDefault) {
            $verifyMail = new RegisterVerify($this->user);
            Mailgun::send($verifyMail->viewName, $verifyMail->params(), function($message) use($email) {
                $message->to($email)->subject("Bigbigads:Please verify your email address")->tag(['registerVerify']);
            });
        } else {
            Mail::to($email)->send(new RegisterVerify($this->user));//发送验证邮件
        }
        /* if ($useMail2) { */
        /*     Mail::setSwiftMailer($backup); */
        /* } */
    }
}
