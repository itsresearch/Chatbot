<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\ConversationController;
use App\Models\Conversation;
use App\Models\Message;


Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Admin Dashboard Routes
    Route::redirect('/admin', '/admin/dashboard');
    
    Route::get('/admin/dashboard', function () {
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
    })->name('admin.dashboard');
    
    Route::get('/admin/conversations', [ConversationController::class, 'index'])->name('admin.conversations');
    Route::get('/admin/conversations/list', [ConversationController::class, 'conversationsList'])->name('admin.conversations.list');
    
    Route::get('/admin/chat/{conversation}', [ConversationController::class, 'show'])->name('admin.chat');
    Route::get('/admin/chat/{conversation}/content', [ConversationController::class, 'chatContent'])->name('admin.chat.content');
    Route::post('/admin/chat/{conversation}/mark-viewed', [ConversationController::class, 'markViewed'])->name('admin.chat.mark-viewed');
    Route::get('/admin/chat/{conversation}/messages', [ConversationController::class, 'messages'])->name('admin.chat.messages');
    Route::post('/admin/chat/{conversation}/send', [ConversationController::class, 'send'])->name('admin.chat.send');
});

