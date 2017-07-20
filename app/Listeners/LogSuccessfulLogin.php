<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\LogAction;

use Jenssegers\Agent\Agent;
use App\ActionLog;
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
        $agent = new Agent();
        if ($agent->isMobile() || $agent->isTablet()) {
            dispatch(new LogAction(ActionLog::ACTION_USER_LOGIN_MOBILE, json_encode(["name" => $user->name, "email" => $user->email]), "", $user->id, Request()->ip() ));
        } else {
            dispatch(new LogAction(ActionLog::ACTION_USER_LOGIN, json_encode(["name" => $user->name, "email" => $user->email]), "", $user->id, Request()->ip() ));
        }
    }
}
