<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class RegisterVerify extends Mailable
{
    use Queueable, SerializesModels;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    protected function params()
    {
        $user = $this->user;
        $host = config('app.url');
        return ['name' => $user->name,
                'link' => "{$host}/register_verify?token={$user->verify_token}&email={$user->email}"
               ];
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.register_verify')
            ->with($this->params())->subject(config('app.name') . ":Please Verify Your Password");
    }
}
