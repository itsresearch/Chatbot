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
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.conversations.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $conversation->load(['messages', 'visitor', 'website']);

        $conversations = Conversation::with(['visitor', 'website', 'messages'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.conversations.show', compact('conversation', 'conversations'));
    }

    public function messages(Request $request, Conversation $conversation)
    {
        $afterId = (int) $request->query('after_id', 0);

        $messages = $conversation->messages()
            ->when($afterId > 0, function ($query) use ($afterId) {
                $query->where('id', '>', $afterId);
            })
            ->orderBy('created_at')
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
        ]);

        event(new MessageSent($message));

        return response()->json([
            'message' => $message,
            'status' => $conversation->status,
        ]);
    }
}