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
        $website = Website::where('api_key', $request->api_key)->firstOrFail();

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

        //  Intelligent bot logic (skip if admin took over)
        if ($conversation->status !== 'human') {
            $userMessage = strtolower($request->message);
            $botReply = $this->generateBotReply($userMessage);

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'bot',
                'message' => $botReply
            ]);
        }

        $messages = $conversation->messages()->orderBy('created_at')->get();

        return response()->json([
            'conversation_id' => $conversation->id,
            'messages' => $messages
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

        // Pricing responses
        if (preg_match('/\b(price|cost|pricing|free|paid|subscription|plan|how much)\b/i', $message)) {
            return 'We offer flexible pricing plans to suit different needs. For detailed pricing information, please visit our website or contact our sales team.';
        }

        // Account/Login responses
        if (preg_match('/\b(login|password|forgot|sign in|account|username|authentication)\b/i', $message)) {
            return 'For account-related issues, you can reset your password using the "Forgot Password" link on the login page. If you need further assistance, our team is here to help!';
        }

        // Product/Feature questions
        if (preg_match('/\b(feature|product|how to|tutorial|guide|documentation)\b/i', $message)) {
            return 'I can help with that! Could you be more specific about which feature or product you\'d like to know more about?';
        }

        // Gratitude responses
        if (preg_match('/\b(thanks|thank you|appreciate|grateful|good|great|perfect|awesome|excellent)\b/i', $message)) {
            return 'You\'re welcome! 😊 Is there anything else I can help you with?';
        }

        // Availability/Hours
        if (preg_match('/\b(when|available|hours|open|schedule|timing)\b/i', $message)) {
            return 'We\'re available 24/7 to help! However, our support team responds during business hours (Mon-Fri, 9AM-5PM EST). Feel free to leave a message anytime!';
        }

        // Complaint/Feedback
        if (preg_match('/\b(complaint|complain|unhappy|disappointed|angry|bad|terrible|worst|issue)\b/i', $message)) {
            return 'We\'re sorry to hear that you\'re having a bad experience. We take your feedback seriously. Could you tell us more details so we can help resolve this?';
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
        // Validate input
        $request->validate([
            'api_key' => 'required|string',
            'visitor_token' => 'required|string',
            'conversation_id' => 'required|integer',
            'after_id' => 'sometimes|integer',
        ]);

        // Identify website
        $website = Website::where('api_key', $request->api_key)->firstOrFail();

        // Identify visitor
        $visitor = Visitor::where([
            'visitor_token' => $request->visitor_token,
            'website_id' => $website->id
        ])->firstOrFail();

        // Get conversation
        $conversation = Conversation::where([
            'id' => $request->conversation_id,
            'visitor_id' => $visitor->id,
            'website_id' => $website->id
        ])->firstOrFail();

        // Fetch messages after specified ID
        $afterId = $request->input('after_id', 0);
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
}
