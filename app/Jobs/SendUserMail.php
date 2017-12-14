<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Mailgun;
use Swift_SmtpTransport;
use Swift_Mailer;

class SendUserMail implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $user;
    protected $mailable;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $mailable)
    {
        $this->user = $user;
        $this->mailable = $mailable;
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

        $res = app('app.service.user')->sendMail($email, $this->mailable);
        return $res;
    }
}
