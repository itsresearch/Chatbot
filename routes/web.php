<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboard;
use App\Http\Controllers\SuperAdmin\ClientController;
use App\Http\Controllers\Client\DashboardController as ClientDashboard;
use App\Http\Controllers\Client\ConversationController;
use App\Http\Controllers\Client\WebsiteController;
use App\Http\Controllers\Client\ChatbotCategoryController;
use App\Http\Controllers\Client\ChatbotServiceController;
use App\Http\Controllers\Client\ChatbotSubServiceController;
use App\Http\Controllers\Client\UtilityController;

// ── Public ──────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

// ── Chat file download (accessible by admin or visitor token) ──
Route::get('/chat-files/{message}', [UtilityController::class, 'downloadChatFile'])->name('chat.file');

// ── Redirect after login (by role) ─────────────────────────
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        return app(\App\Http\Middleware\RedirectByRole::class)->handle(request(), fn () => redirect('/'));
    })->name('dashboard');

    // ── Super Admin Routes ──────────────────────────────────
    Route::prefix('superadmin')->as('superadmin.')->middleware('superadmin')->group(function () {
        Route::get('/', fn () => redirect()->route('superadmin.dashboard'));
        Route::get('dashboard', [SuperAdminDashboard::class, 'index'])->name('dashboard');

        // Client CRUD
        Route::resource('clients', ClientController::class)->parameters(['clients' => 'client']);
        Route::post('clients/{client}/toggle-status', [ClientController::class, 'toggleStatus'])->name('clients.toggle-status');
    });

    // ── Client Routes ───────────────────────────────────────
    Route::prefix('client')->as('client.')->middleware('client')->group(function () {
        Route::get('/', fn () => redirect()->route('client.dashboard'));
        Route::get('dashboard', [ClientDashboard::class, 'index'])->name('dashboard');

        // ── Website Switcher ────────────────────────────────
        Route::post('switch-website', [UtilityController::class, 'switchWebsite'])->name('switch-website');

        // ── Notifications API ───────────────────────────────
        Route::get('notifications/unread-count', [UtilityController::class, 'unreadCount'])->name('notifications.unread-count');

        Route::get('notifications/recent', [UtilityController::class, 'recentNotifications'])->name('notifications.recent');

        // Websites
        Route::resource('websites', WebsiteController::class);
        Route::post('websites/{website}/regenerate-key', [WebsiteController::class, 'regenerateKey'])->name('websites.regenerate-key');

        // Conversations
        Route::get('conversations', [ConversationController::class, 'index'])->name('conversations');
        Route::get('conversations/list', [ConversationController::class, 'conversationsList'])->name('conversations.list');
        Route::get('chat/{conversation}', [ConversationController::class, 'show'])->name('chat');
        Route::get('chat/{conversation}/content', [ConversationController::class, 'chatContent'])->name('chat.content');
        Route::post('chat/{conversation}/mark-viewed', [ConversationController::class, 'markViewed'])->name('chat.mark-viewed');
        Route::get('chat/{conversation}/messages', [ConversationController::class, 'messages'])->name('chat.messages');
        Route::post('chat/{conversation}/send', [ConversationController::class, 'send'])->name('chat.send');

        // ── Chatbot Knowledge Base Management ───────────────
        Route::prefix('chatbot')->as('chatbot.')->group(function () {
            // Categories
            Route::resource('categories', ChatbotCategoryController::class);

            // Services (nested under category)
            Route::get('categories/{category}/services', [ChatbotServiceController::class, 'index'])->name('services.index');
            Route::get('categories/{category}/services/create', [ChatbotServiceController::class, 'create'])->name('services.create');
            Route::post('categories/{category}/services', [ChatbotServiceController::class, 'store'])->name('services.store');
            Route::get('services/{service}', [ChatbotServiceController::class, 'show'])->name('services.show');
            Route::get('services/{service}/edit', [ChatbotServiceController::class, 'edit'])->name('services.edit');
            Route::put('services/{service}', [ChatbotServiceController::class, 'update'])->name('services.update');
            Route::delete('services/{service}', [ChatbotServiceController::class, 'destroy'])->name('services.destroy');

            // Sub-services (nested under service)
            Route::get('services/{service}/sub-services', [ChatbotSubServiceController::class, 'index'])->name('sub-services.index');
            Route::get('services/{service}/sub-services/create', [ChatbotSubServiceController::class, 'create'])->name('sub-services.create');
            Route::post('services/{service}/sub-services', [ChatbotSubServiceController::class, 'store'])->name('sub-services.store');
            Route::get('sub-services/{subService}', [ChatbotSubServiceController::class, 'show'])->name('sub-services.show');
            Route::get('sub-services/{subService}/edit', [ChatbotSubServiceController::class, 'edit'])->name('sub-services.edit');
            Route::put('sub-services/{subService}', [ChatbotSubServiceController::class, 'update'])->name('sub-services.update');
            Route::delete('sub-services/{subService}', [ChatbotSubServiceController::class, 'destroy'])->name('sub-services.destroy');
        });
    });
});