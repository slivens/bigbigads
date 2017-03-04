<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\ActionLog;

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
        ActionLog::log(ActionLog::TYPE_USER_LOGOUT, json_encode(["name" => $user->name,  "email" => $user->email]), "", $user->id);
    }
}
