<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index()
    {
        $conversations = Conversation::with(['visitor', 'website', 'messages'])
            ->whereNotNull('visitor_id')
            ->orderByRaw('CASE WHEN last_message_at > COALESCE(admin_viewed_at, "1970-01-01") THEN 0 ELSE 1 END')
            ->orderBy('last_message_at', 'desc')
            ->get();

        if ($conversations->isNotEmpty()) {
            return redirect()->route('admin.chat', $conversations->first());
        }

        return view('admin.conversations.index', compact('conversations'));
    }

    /**
     * Return the conversations list as JSON for sidebar live-refresh.
     */
    public function conversationsList()
    {
        $conversations = Conversation::with(['visitor', 'website'])
            ->whereNotNull('visitor_id')
            ->whereHas('visitor')
            ->orderByRaw('CASE WHEN last_message_at > COALESCE(admin_viewed_at, "1970-01-01") THEN 0 ELSE 1 END')
            ->orderBy('last_message_at', 'desc')
            ->get();

        $result = $conversations->map(function ($conv) {
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
                'last_message_at' => $conv->last_message_at?->toIso8601String(),
                'last_message_at_human' => $conv->last_message_at?->diffForHumans(),
                'last_message_at_time' => $conv->last_message_at?->format('H:i'),
                'message_count' => $conv->messages()->count(),
            ];
        });

        return response()->json($result);
    }

    public function show(Conversation $conversation)
    {
        // Mark this conversation as viewed by admin when opened
        $conversation->update(['admin_viewed_at' => now()]);
        $conversation->load(['messages', 'visitor', 'website']);

        $conversations = Conversation::with(['visitor', 'website', 'messages'])
            ->whereNotNull('visitor_id')
            ->orderByRaw('CASE WHEN last_message_at > COALESCE(admin_viewed_at, "1970-01-01") THEN 0 ELSE 1 END')
            ->orderBy('last_message_at', 'desc')
            ->get();

        return view('admin.conversations.show', compact('conversation', 'conversations'));
    }

    public function chatContent(Conversation $conversation)
    {
        $conversation->load(['messages', 'visitor', 'website']);
        return view('admin.conversations.chat-content', compact('conversation'));
    }

    public function markViewed(Conversation $conversation)
    {
        $conversation->update(['admin_viewed_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function messages(Request $request, Conversation $conversation)
    {
        $afterId = (int) $request->query('after_id', 0);

        $messages = $conversation->messages()
            ->when($afterId > 0, function ($query) use ($afterId) {
                $query->where('id', '>', $afterId);
            })
            ->orderBy('id')
            ->get();

        return response()->json([
            'messages' => $messages,
            'status' => $conversation->status,
        ]);
    }

    public function send(Request $request, Conversation $conversation)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'admin',
            'sender_id' => $request->user()->id,
            'message' => $validated['message'],
        ]);

        $conversation->update([
            'status' => 'human',
            'last_message_at' => now(),
            'admin_viewed_at' => now(),
        ]);

        event(new MessageSent($message));

        return response()->json([
            'message' => $message,
            'status' => $conversation->status,
        ]);
    }
}