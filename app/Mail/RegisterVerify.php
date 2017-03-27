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

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = $this->user;
        $host = str_replace(request()->path(), "", request()->url()); 
        return $this->view('emails.register_verify')
            ->with([
                'name' => $user->name,
                'link' => "{$host}registerVerify?token={$user->verify_token}&email={$user->email}"
                    ]);
    }
}
