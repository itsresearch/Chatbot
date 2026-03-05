<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Support\Str;

/**
 * Broadcast to the website owner when a visitor sends a new message.
 * This enables live notification badges, toasts, and sounds in the admin panel.
 */
class NewVisitorMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public array $data;
    private int $userId;

    public function __construct(Message $message, int $userId)
    {
        $this->userId = $userId;

        $conversation = $message->conversation;
        $website = $conversation->website;
        $visitor = $conversation->visitor;

        $this->data = [
            'message_id'      => $message->id,
            'conversation_id' => $conversation->id,
            'website_id'      => $website->id,
            'website_name'    => $website->name,
            'website_domain'  => $website->domain,
            'visitor_token'   => $visitor?->visitor_token,
            'visitor_label'   => $visitor ? 'Visitor ' . substr($visitor->visitor_token, 0, 8) : 'Unknown',
            'message_preview' => Str::limit($message->message, 80),
            'timestamp'       => $message->created_at->toIso8601String(),
        ];
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('App.Models.User.' . $this->userId);
    }

    public function broadcastAs(): string
    {
        return 'NewVisitorMessage';
    }

    public function broadcastWith(): array
    {
        return $this->data;
    }
}
