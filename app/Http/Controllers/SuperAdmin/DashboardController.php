<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Website;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\ConversationService;

class DashboardController extends Controller
{
    public function __construct(protected ConversationService $conversationService) {}

    public function index()
    {
        $totalClients = User::where('role', 'client')->count();
        $activeClients = User::where('role', 'client')->where('is_active', true)->count();
        $totalWebsites = Website::count();
        $totalConversations = Conversation::count();
        $totalMessages = Message::count();
        $messagesToday = Message::where('created_at', '>=', now()->startOfDay())->count();

        $recentClients = User::where('role', 'client')
            ->withCount('websites')
            ->latest()
            ->limit(5)
            ->get();

        $recentConversations = Conversation::with(['visitor', 'website.owner'])
            ->orderByDesc('last_message_at')
            ->limit(5)
            ->get();

        return view('superadmin.dashboard', compact(
            'totalClients', 'activeClients', 'totalWebsites',
            'totalConversations', 'totalMessages', 'messagesToday',
            'recentClients', 'recentConversations'
        ));
    }
}
