<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;

class DashboardController extends Controller
{
    public function index()
    {
        $totalConversations = Conversation::count();
        $humanConversations = Conversation::where('status', 'human')->count();
        $botConversations = Conversation::where('status', 'bot')->count();

        $today = now()->startOfDay();
        $messagesToday = Message::where('created_at', '>=', $today)->count();

        $recentConversations = Conversation::with(['visitor', 'website', 'messages' => function ($query) {
            $query->latest();
        }])
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact(
            'totalConversations',
            'humanConversations',
            'botConversations',
            'messagesToday',
            'recentConversations'
        ));
    }
}
