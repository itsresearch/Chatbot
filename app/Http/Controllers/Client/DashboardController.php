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
        $websiteIds = $user->websiteIds();

        $stats = $this->conversationService->getStats($websiteIds);
        $websites = $user->websites()->withCount(['conversations', 'visitors'])->get();

        $recentConversations = $this->conversationService->getConversations($websiteIds, 6);

        return view('client.dashboard', compact('stats', 'websites', 'recentConversations'));
    }
}
