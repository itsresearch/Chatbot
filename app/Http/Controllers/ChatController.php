<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Website;
use App\Models\Visitor;
use App\Models\Conversation;
use App\Models\Message;

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

        //  Update last_message_at
        $conversation->update(['last_message_at' => now()]);

        //  Only return newly created messages (not the entire history)
        $newMessages = [$visitorMessage];

        //  Bot replies only when admin hasn't taken over
        if ($conversation->status !== 'human') {
            $botReply = $this->generateBotReply(strtolower($request->message));

            $botMessage = Message::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'bot',
                'message' => $botReply
            ]);

            $newMessages[] = $botMessage;
        }

        return response()->json([
            'conversation_id' => $conversation->id,
            'messages' => $newMessages,
            'status' => $conversation->status,
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
            return $greetings[array_rand($greetings)];
        }

        // Help/Support responses
        if (preg_match('/\b(help|support|assist|issue|problem|error|bug|not working)\b/i', $message)) {
            return 'I\'d be happy to help! Could you please describe the issue you\'re facing? I\'ll do my best to assist you.';
        }

        // Contact/Information responses
        if (preg_match('/\b(contact|email|phone|call|reach|get in touch|information|address)\b/i', $message)) {
            return 'You can reach our support team at support@company.com or call us at 1-800-123-4567. We\'re available Monday-Friday, 9AM-5PM EST.';
        }



        // Gratitude responses
        if (preg_match('/\b(thanks|thank you|appreciate|grateful|good|great|perfect|awesome|excellent)\b/i', $message)) {
            return 'You\'re welcome! 😊 Is there anything else I can help you with?';
        }

        // Availability/Hours
        if (preg_match('/\b(when|available|hours|open|schedule|timing)\b/i', $message)) {
            return 'We\'re available 24/7 to help! However, our support team responds during business hours (Mon-Fri, 9AM-5PM EST). Feel free to leave a message anytime!';
        }


        // Default response for unmatched messages
        $defaultResponses = [
            'I understand! Let me help you with that. Could you provide more details?',
            'That\'s a great question! What specifically would you like to know?',
            'I\'m here to help! Can you tell me more about what you need?',
            'Thanks for your message. How can I best assist you?',
            'Got it! Let me know how I can be of service.',
        ];

        return $defaultResponses[array_rand($defaultResponses)];
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
