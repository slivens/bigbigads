<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\LogAction;

class LogSuccessfulLogout 
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
    public function handle(Logout $event)
    {
        $user = $event->user;
        if (is_null($user))
            return;
        dispatch(new LogAction("USER_LOGOUT", json_encode(["name" => $user->name,  "email" => $user->email]), "", $user->id, Request()->ip()));
    }
}
