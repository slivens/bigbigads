<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Mailgun;
use App\Mail\PayHelpMail;
use Swift_SmtpTransport;
use Swift_Mailer;

class SendPayHelpMail implements ShouldQueue
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

        $res = app('app.service.user')->sendMail($email, new PayHelpMail($this->user), [
            'tags' => [
                'registerVerify'
            ]
        ]);
        return $res;
    }
}
