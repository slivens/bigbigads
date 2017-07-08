<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\LogAction;

use BrowserDetect;
class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SomeEvent  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;
        if (BrowserDetect::isMobile()) {
            dispatch(new LogAction("USER_LOGIN_MOBILE", json_encode(["name" => $user->name, "email" => $user->email]), "", $user->id, Request()->ip() ));
        } else {
            dispatch(new LogAction("USER_LOGIN", json_encode(["name" => $user->name, "email" => $user->email]), "", $user->id, Request()->ip() ));
        }
    }
}
