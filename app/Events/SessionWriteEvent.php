<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Auth\Guard;
use Log;

class SessionWriteEvent
{
    use InteractsWithSockets, SerializesModels;
    public $sessionId;
    public $session;
    public $payload;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $sessionId, $session)
    {
        $this->sessionId = $sessionId;
        $this->session = $session;
        $payload = [];
        $payload['user_id'] = app(Guard::class)->id();
        $payload['ip_address'] = app('request')->ip();
        $payload['user_agent'] = substr(
                (string) app('request')->header('User-Agent'), 0, 500
            );
        $this->payload = $payload;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
