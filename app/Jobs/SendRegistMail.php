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
class SendRegistMail implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $user;
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
        $email = $this->user->email;
        $useMail2 = false;
        //暂不启用
        /* if (preg_match('/qq\.com$/', $email)) { */
        /*     $useMail2 = true; */
        /* } */
        if ($useMail2) {
            $backup = Mail::getSwiftMailer();
            $config = config('mail.addons.mail2');

            $transport = Swift_SmtpTransport::newInstance($config['host'], $config['port'], $config['encryption']);
            $transport->setUsername($config['username']);
            $transport->setPassword($config['password']);
            $mail2 = new Swift_Mailer($transport);
            Mail::setSwiftMailer($mail2);
        }

        Mail::to($email)->send(new RegisterVerify($this->user));//发送验证邮件

        if ($useMail2) {
            Mail::setSwiftMailer($backup);
        }
    }
}
