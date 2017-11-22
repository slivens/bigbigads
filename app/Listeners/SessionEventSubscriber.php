<?php

namespace App\Listeners;

use App\Events\SessionOpenEvent;
use App\Events\SessionWriteEvent;
use App\Events\SessionDestroyEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use Voyager;

class SessionEventSubscriber implements ShouldQueue
{
    use InteractsWithQueue;
    private $sessionService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(\App\Contracts\SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }


    public function onSessionOpen($event)
    {
        Log::debug("on session start");
    }

    /**
     * Session有写入时检查
     */
    public function onSessionWrite($event)
    {
        Log::debug("on session write");
        $globalSessionCount = Voyager::setting('global_session_count');
        if (!$globalSessionCount)
            return;
    }

    public function onSessionDestroy($event)
    {
        Log::debug("on session destroy");
    }

    public function subscribe($events)
    {
        $events->listen(
            SessionOpenEvent::class,
            self::class . '@onSessionOpen'
        );

        $events->listen(
            SessionWriteEvent::class,
            self::class . '@onSessionWrite'
        );

        $events->listen(
            SessionDestroyEvent::class,
            self::class . '@onSessionDestroy'
        );
    }
}
