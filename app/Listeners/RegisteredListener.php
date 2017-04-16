<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\LogAction;

class RegisteredListener
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
    public function handle(Registered $event)
    {
        $user = $event->user;
        dispatch(new LogAction("USER_REGISTERED", json_encode(["name" => $user->name, "email" => $user->email]), "", $user->id, Request()->ip()));
        //创建默认的收藏夹
        $bookmark = new \App\Bookmark;
        $bookmark->uid = $user->id;
        $bookmark->name = "default";
        $bookmark->save();
    }
}
