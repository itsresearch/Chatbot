<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Services\ConversationService;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function __construct(protected ConversationService $conversationService) {}

    public function index()
    {
        $websiteIds = auth()->user()->websiteIds();
        $conversations = $this->conversationService->getConversations($websiteIds);

        if ($conversations->isNotEmpty()) {
            return redirect()->route('client.chat', $conversations->first());
        }

        return view('client.conversations.index', compact('conversations'));
    }

    public function conversationsList()
    {
        $websiteIds = auth()->user()->websiteIds();
        return response()->json($this->conversationService->conversationsListData($websiteIds));
    }

    public function show(Conversation $conversation)
    {
        $this->authorizeConversation($conversation);

        $conversation->update(['admin_viewed_at' => now()]);
        $conversation->load(['messages', 'visitor', 'website']);

        $websiteIds = auth()->user()->websiteIds();
        $conversations = $this->conversationService->getConversations($websiteIds);

        return view('client.conversations.show', compact('conversation', 'conversations'));
    }

    public function chatContent(Conversation $conversation)
    {
        $this->authorizeConversation($conversation);
        $conversation->load(['messages', 'visitor', 'website']);
        return view('client.conversations.chat-content', compact('conversation'));
    }

    public function markViewed(Conversation $conversation)
    {
        $this->authorizeConversation($conversation);
        $conversation->update(['admin_viewed_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function messages(Request $request, Conversation $conversation)
    {
        $this->authorizeConversation($conversation);
        $afterId = (int) $request->query('after_id', 0);
        $messages = $this->conversationService->getNewMessages($conversation, $afterId);

        return response()->json([
            'messages' => $messages,
            'status' => $conversation->status,
        ]);
    }

    public function send(Request $request, Conversation $conversation)
    {
        $this->authorizeConversation($conversation);

        $validated = $request->validate(['message' => 'required|string']);
        $message = $this->conversationService->sendAdminMessage(
            $conversation, $validated['message'], $request->user()->id
        );

        return response()->json([
            'message' => $message,
            'status' => $conversation->fresh()->status,
        ]);
    }

    protected function authorizeConversation(Conversation $conversation): void
    {
        $websiteIds = auth()->user()->websiteIds();
        abort_if(
            !$this->conversationService->authorizeConversation($conversation, $websiteIds),
            403, 'Unauthorized.'
        );
    }
}
