<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Website;
use App\Models\Visitor;
use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        //  Validate input
        $request->validate([
            'api_key' => 'required|string',
            'visitor_token' => 'required|string',
            'message' => 'required|string',
        ]);

        //  Identify website
        $website = Website::where('api_key', $request->api_key)->first();
        if (!$website) {
            return response()->json(['error' => 'Invalid API key'], 403);
        }

        //  Identify visitor
        $visitor = Visitor::firstOrCreate([
            'visitor_token' => $request->visitor_token,
            'website_id' => $website->id
        ]);

        //  Get or create conversation
        $conversation = Conversation::firstOrCreate(
            [
                'website_id' => $website->id,
                'visitor_id' => $visitor->id,
            ],
            [
                'status' => 'bot',
            ]
        );

        //  Save visitor message
        $visitorMessage = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'visitor',
            'message' => $request->message
        ]);

        //  Broadcast visitor message
        broadcast(new MessageSent($visitorMessage));

        //  Update last_message_at
        $conversation->update(['last_message_at' => now()]);

        //  Only return newly created messages (not the entire history)
        $newMessages = [$visitorMessage];

        //  Bot replies only when admin hasn't taken over
        if ($conversation->status !== 'human') {
            $botResponse = $this->generateBotReply(strtolower($request->message));

            $botMessage = Message::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'bot',
                'message' => $botResponse['message']
            ]);

            //  Broadcast bot message
            broadcast(new MessageSent($botMessage));

            $newMessages[] = $botMessage;

            // Redirect to human agent if bot doesn't have a specific answer
            if ($botResponse['redirect_to_human']) {
                $conversation->update(['status' => 'human']);
            }
        }

        return response()->json([
            'conversation_id' => $conversation->id,
            'messages' => $newMessages,
            'status' => $conversation->fresh()->status,
        ]);
    }

    private function generateBotReply($message)
    {
        // Greeting responses
        if (preg_match('/\b(hello|hi|hey|greetings|good morning|good afternoon|good evening)\b/i', $message)) {
            $greetings = [
                'Hey there! 👋 How can I assist you today?',
                'Hello! Welcome to our chat support. What can I help you with?',
                'Hi! Thanks for reaching out. What can we do for you?',
                'Greetings! How may I be of service?',
            ];
            return ['message' => $greetings[array_rand($greetings)], 'redirect_to_human' => false];
        }

        // Help/Support responses
        if (preg_match('/\b(help|support|assist|issue|problem|error|bug|not working)\b/i', $message)) {
            return ['message' => 'I\'d be happy to help! Could you please describe the issue you\'re facing? I\'ll do my best to assist you.', 'redirect_to_human' => false];
        }

        // Contact/Information responses
        if (preg_match('/\b(contact|email|phone|call|reach|get in touch|information|address)\b/i', $message)) {
            return ['message' => 'You can reach our support team at support@company.com or call us at 1-800-123-4567. We\'re available Monday-Friday, 9AM-5PM EST.', 'redirect_to_human' => false];
        }

        // Gratitude responses
        if (preg_match('/\b(thanks|thank you|appreciate|grateful|good|great|perfect|awesome|excellent)\b/i', $message)) {
            return ['message' => 'You\'re welcome! 😊 Is there anything else I can help you with?', 'redirect_to_human' => false];
        }

        // Availability/Hours
        if (preg_match('/\b(when|available|hours|open|schedule|timing)\b/i', $message)) {
            return ['message' => 'We\'re available 24/7 to help! However, our support team responds during business hours (Mon-Fri, 9AM-5PM EST). Feel free to leave a message anytime!', 'redirect_to_human' => false];
        }

        // Goodbye responses
        if (preg_match('/\b(bye|goodbye|see you|take care|later)\b/i', $message)) {
            return ['message' => 'Goodbye! 👋 Feel free to reach out anytime you need help. Take care!', 'redirect_to_human' => false];
        }

        // Bot doesn't have a specific answer - redirect to human agent
        $redirectResponses = [
            'I appreciate your question! Let me connect you with a human agent who can better assist you. Please hold on... 🙋',
            'That\'s a great question that requires human expertise. I\'m redirecting you to one of our support agents. They\'ll be with you shortly!',
            'I want to make sure you get the best help possible. Let me transfer you to a human agent who can assist you further. 🤝',
            'This is beyond my capabilities. I\'m connecting you with a live support agent who will be able to help you better!',
        ];

        return ['message' => $redirectResponses[array_rand($redirectResponses)], 'redirect_to_human' => true];
    }

    public function fetchMessages(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'visitor_token' => 'required|string',
            'conversation_id' => 'required|integer',
            'after_id' => 'sometimes|integer',
        ]);

        $conversationId = (int) $request->conversation_id;
        $afterId = (int) $request->input('after_id', 0);

        // Single join query validates ownership (1 query instead of 3)
        $conversation = Conversation::query()
            ->join('visitors', 'visitors.id', '=', 'conversations.visitor_id')
            ->join('websites', 'websites.id', '=', 'conversations.website_id')
            ->where('conversations.id', $conversationId)
            ->where('visitors.visitor_token', $request->visitor_token)
            ->where('websites.api_key', $request->api_key)
            ->select('conversations.id', 'conversations.status')
            ->first();

        if (!$conversation) {
            return response()->json(['messages' => [], 'status' => null]);
        }

        // Only fetch messages newer than what client already has
        $messages = $afterId > 0
            ? Message::where('conversation_id', $conversationId)
                ->where('id', '>', $afterId)
                ->orderBy('id')
                ->get()
            : collect();

        return response()->json([
            'messages' => $messages,
            'status' => $conversation->status,
        ]);
    }
}
