<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class MessageSent implements ShouldBroadcast
{
    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    use Dispatchable, InteractsWithSockets;

    public function broadcastOn()
    {
        return new Channel('chat.' . $this->message->conversation_id);
    }

    public function broadcastAs()
    {
        return 'MessageSent';
    }
}