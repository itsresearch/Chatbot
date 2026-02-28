<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Collection;

/**
 * Shared conversation logic used by both SuperAdmin & Client controllers (DRY).
 */
class ConversationService
{
    /**
     * Get conversations scoped to given website IDs with consistent ordering.
     */
    public function getConversations(array $websiteIds, int $limit = 0): Collection
    {
        $query = Conversation::with(['visitor', 'website', 'messages'])
            ->whereIn('website_id', $websiteIds)
            ->whereNotNull('visitor_id')
            ->orderByRaw('CASE WHEN last_message_at > COALESCE(admin_viewed_at, "1970-01-01") THEN 0 ELSE 1 END')
            ->orderBy('last_message_at', 'desc');

        if ($limit > 0) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Build a consistent JSON-serializable list for sidebar refresh.
     */
    public function conversationsListData(array $websiteIds): array
    {
        $conversations = Conversation::with(['visitor', 'website'])
            ->whereIn('website_id', $websiteIds)
            ->whereNotNull('visitor_id')
            ->whereHas('visitor')
            ->orderByRaw('CASE WHEN last_message_at > COALESCE(admin_viewed_at, "1970-01-01") THEN 0 ELSE 1 END')
            ->orderBy('last_message_at', 'desc')
            ->get();

        return $conversations->map(function ($conv) {
            $lastMessage = $conv->messages()->orderBy('id', 'desc')->first();
            $hasVisitorMessage = $lastMessage && $lastMessage->sender_type === 'visitor';
            $isUnread = $hasVisitorMessage
                && $conv->last_message_at
                && (!$conv->admin_viewed_at || $conv->last_message_at > $conv->admin_viewed_at);

            return [
                'id' => $conv->id,
                'visitor_token' => $conv->visitor->visitor_token ?? null,
                'website_name' => $conv->website->name ?? 'Unknown',
                'status' => $conv->status,
                'is_unread' => $isUnread,
                'last_message' => $lastMessage ? \Illuminate\Support\Str::limit($lastMessage->message, 50) : null,
                'last_message_sender' => $lastMessage ? $lastMessage->sender_type : null,
                'last_message_at' => $conv->last_message_at?->toIso8601String(),
                'last_message_at_human' => $conv->last_message_at?->diffForHumans(),
                'last_message_at_time' => $conv->last_message_at?->format('H:i'),
                'last_message_at_date' => $conv->last_message_at?->format('d M'),
                'message_count' => $conv->messages()->count(),
            ];
        })->toArray();
    }

    /**
     * Verify a conversation belongs to the given website IDs.
     */
    public function authorizeConversation(Conversation $conversation, array $websiteIds): bool
    {
        return in_array($conversation->website_id, $websiteIds);
    }

    /**
     * Fetch new messages for a conversation after a given ID.
     */
    public function getNewMessages(Conversation $conversation, int $afterId = 0)
    {
        return $conversation->messages()
            ->when($afterId > 0, fn ($q) => $q->where('id', '>', $afterId))
            ->orderBy('id')
            ->get();
    }

    /**
     * Admin sends a message in a conversation.
     */
    public function sendAdminMessage(Conversation $conversation, string $messageText, int $userId): Message
    {
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'admin',
            'sender_id' => $userId,
            'message' => $messageText,
        ]);

        $conversation->update([
            'status' => 'human',
            'last_message_at' => now(),
            'admin_viewed_at' => now(),
        ]);

        event(new MessageSent($message));

        return $message;
    }

    /**
     * Dashboard statistics scoped to given website IDs.
     */
    public function getStats(array $websiteIds): array
    {
        $baseQuery = Conversation::whereIn('website_id', $websiteIds);

        return [
            'totalConversations' => (clone $baseQuery)->count(),
            'humanConversations' => (clone $baseQuery)->where('status', 'human')->count(),
            'botConversations' => (clone $baseQuery)->where('status', 'bot')->count(),
            'messagesToday' => Message::whereIn('conversation_id',
                (clone $baseQuery)->pluck('id')
            )->where('created_at', '>=', now()->startOfDay())->count(),
        ];
    }
}
