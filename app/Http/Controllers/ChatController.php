<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Website;
use App\Models\Visitor;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\ChatbotCategory;
use App\Models\ChatbotService;
use App\Models\ChatbotSubService;
use App\Events\MessageSent;
use App\Events\NewVisitorMessage;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'visitor_token' => 'required|string',
            'message' => 'nullable|string',
            'file' => 'nullable|file|max:10240|mimes:' . implode(',', Message::allowedExtensions()),
        ]);

        // At least a message or file must be present
        if (!$request->message && !$request->hasFile('file')) {
            return response()->json(['error' => 'A message or file is required.'], 422);
        }

        $website = Website::where('api_key', $request->api_key)->where('is_active', true)->first();
        if (!$website) {
            return response()->json(['error' => 'Invalid API key or website disabled'], 403);
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

        //  Handle file upload
        $fileData = [];
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $folder = 'chat_files/' . $conversation->id;
            $storedPath = $file->store($folder, 'private');

            $fileData = [
                'file_path' => $storedPath,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ];
        }

        //  Save visitor message
        $visitorMessage = Message::create(array_merge([
            'conversation_id' => $conversation->id,
            'sender_type' => 'visitor',
            'message' => $request->message ?? ($fileData ? '📎 ' . ($fileData['file_name'] ?? 'File') : ''),
        ], $fileData));

        //  Broadcast visitor message
        try {
            broadcast(new MessageSent($visitorMessage));
        } catch (\Exception $e) {
            // Broadcasting may fail if Reverb isn't running — non-critical
        }

        //  Notify the website owner in real-time (private channel)
        try {
            $ownerId = $website->user_id;
            if ($ownerId) {
                broadcast(new NewVisitorMessage($visitorMessage, $ownerId));
            }
        } catch (\Exception $e) {
            // Non-critical — polling fallback will still work
        }

        //  Update last_message_at
        $conversation->update(['last_message_at' => now()]);

        //  Only return newly created messages (not the entire history)
        $newMessages = [$visitorMessage];

        //  Bot replies only when admin hasn't taken over and there's text to process
        if ($conversation->status !== 'human' && $request->message) {
            $botResponse = $this->generateBotReply(strtolower($request->message));

            $botMessage = Message::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'bot',
                'message' => $botResponse['message']
            ]);

            //  Broadcast bot message
            try {
                broadcast(new MessageSent($botMessage));
            } catch (\Exception $e) {
                // Broadcasting may fail if Reverb/Pusher isn't running — non-critical
            }

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

    /**
     * Widget config endpoint — returns per-website customization.
     */
    public function widgetConfig(Request $request)
    {
        $request->validate(['api_key' => 'required|string']);

        $website = Website::where('api_key', $request->api_key)->where('is_active', true)->first();
        if (!$website) {
            return response()->json(['error' => 'Invalid API key'], 403);
        }

        return response()->json([
            'name' => $website->name,
            'welcome_message' => $website->welcome_message,
            'widget_color' => $website->widget_color,
            'widget_color_type' => $website->widget_color_type ?? 'gradient',
            'widget_position' => $website->widget_position ?? 'bottom-right',
            'has_knowledge_base' => ChatbotCategory::forWebsite($website->id)->exists(),
        ]);
    }

    /* ================================================================
     *  Knowledge Base API — Categories / Services / Sub-services
     * ================================================================ */

    /**
     * Fetch active categories for a website.
     */
    public function getCategories(Request $request)
    {
        $request->validate(['api_key' => 'required|string']);

        $website = $this->resolveWebsite($request->api_key);
        if (!$website) {
            return response()->json(['error' => 'Invalid API key'], 403);
        }

        $categories = ChatbotCategory::forWebsite($website->id)
            ->orderBy('name')
            ->select('id', 'name', 'description')
            ->get();

        return response()->json(['categories' => $categories]);
    }

    /**
     * Fetch active services under a category.
     */
    public function getServices(Request $request)
    {
        $request->validate([
            'api_key'     => 'required|string',
            'category_id' => 'required|integer',
        ]);

        $website = $this->resolveWebsite($request->api_key);
        if (!$website) {
            return response()->json(['error' => 'Invalid API key'], 403);
        }

        // Verify category belongs to this website
        $category = ChatbotCategory::where('id', $request->category_id)
            ->where('website_id', $website->id)
            ->first();

        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        $services = $category->services()
            ->orderBy('name')
            ->select('id', 'category_id', 'name', 'description')
            ->get();

        return response()->json([
            'category' => [
                'id'   => $category->id,
                'name' => $category->name,
            ],
            'services' => $services,
        ]);
    }

    /**
     * Fetch active sub-services under a service.
     */
    public function getSubServices(Request $request)
    {
        $request->validate([
            'api_key'    => 'required|string',
            'service_id' => 'required|integer',
        ]);

        $website = $this->resolveWebsite($request->api_key);
        if (!$website) {
            return response()->json(['error' => 'Invalid API key'], 403);
        }

        $service = ChatbotService::where('id', $request->service_id)
            ->whereHas('category', fn ($q) => $q->where('website_id', $website->id))
            ->first();

        if (!$service) {
            return response()->json(['error' => 'Service not found'], 404);
        }

        $subServices = $service->subServices()
            ->orderBy('name')
            ->select('id', 'service_id', 'name', 'short_description')
            ->get();

        return response()->json([
            'service' => [
                'id'   => $service->id,
                'name' => $service->name,
            ],
            'sub_services' => $subServices,
        ]);
    }

    /**
     * Fetch full detail/description of a sub-service.
     */
    public function getSubServiceDetail(Request $request)
    {
        $request->validate([
            'api_key'        => 'required|string',
            'sub_service_id' => 'required|integer',
        ]);

        $website = $this->resolveWebsite($request->api_key);
        if (!$website) {
            return response()->json(['error' => 'Invalid API key'], 403);
        }

        $subService = ChatbotSubService::where('id', $request->sub_service_id)
            ->whereHas('service.category', fn ($q) => $q->where('website_id', $website->id))
            ->first();

        if (!$subService) {
            return response()->json(['error' => 'Sub-service not found'], 404);
        }

        return response()->json([
            'sub_service' => [
                'id'                => $subService->id,
                'name'              => $subService->name,
                'short_description' => $subService->short_description,
                'detail_content'    => $subService->detail_content,
            ],
        ]);
    }

    /* ── Helpers ──────────────────────────────────── */

    private function resolveWebsite(string $apiKey): ?Website
    {
        return Website::where('api_key', $apiKey)
            ->where('is_active', true)
            ->first();
    }
}
