<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SessionWriteEvent
{
    use InteractsWithSockets, SerializesModels;
    public $sessionId;
    public $session;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $sessionId, $session)
    {
        $this->sessionId = $sessionId;
        $this->session = $session;
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
