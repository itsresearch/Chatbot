<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\ConversationService;

class DashboardController extends Controller
{
    public function __construct(protected ConversationService $conversationService) {}

    public function index()
    {
        $user = auth()->user();
        $websiteIds = $user->activeWebsiteIds();

        $stats = $this->conversationService->getStats($websiteIds);

        // Websites table always shows all; stats/conversations respect active filter
        $websites = $user->websites()->withCount(['conversations', 'visitors'])->get();

        $recentConversations = $this->conversationService->getConversations($websiteIds, 6);

        return view('client.dashboard', compact('stats', 'websites', 'recentConversations'));
    }
}
