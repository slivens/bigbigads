<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SessionOpenEvent
{
    use InteractsWithSockets, SerializesModels;
    public $savePath;
    public $sessionName;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($savePath, $sessionName)
    {
        $this->savePath = $savePath;
        $this->sessionName = $sessionName;
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
