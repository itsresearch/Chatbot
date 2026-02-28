<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboard;
use App\Http\Controllers\SuperAdmin\ClientController;
use App\Http\Controllers\Client\DashboardController as ClientDashboard;
use App\Http\Controllers\Client\ConversationController;
use App\Http\Controllers\Client\WebsiteController;

// ── Public ──────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

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
    });
});