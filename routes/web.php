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

// ── Public ──────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

// ── Chat file download (accessible by admin or visitor token) ──
Route::get('/chat-files/{message}', function (\App\Models\Message $message, \Illuminate\Http\Request $request) {
    if (!$message->file_path || !\Illuminate\Support\Facades\Storage::disk('local')->exists($message->file_path)) {
        abort(404, 'File not found.');
    }

    // Admin access (logged-in and owns the website)
    $user = auth()->user();
    if ($user) {
        $websiteIds = $user->websiteIds();
        $conversation = $message->conversation;
        if ($conversation && in_array($conversation->website_id, $websiteIds)) {
            return response()->file(
                storage_path('app/private/' . str_replace('private/', '', $message->file_path)),
                [
                    'Content-Type' => $message->file_type ?? 'application/octet-stream',
                    'Content-Disposition' => 'inline; filename="' . ($message->file_name ?? 'file') . '"',
                ]
            );
        }
    }

    // Visitor access (via visitor_token in query string)
    $visitorToken = $request->query('visitor_token');
    if ($visitorToken) {
        $conversation = $message->conversation;
        if ($conversation && $conversation->visitor && $conversation->visitor->visitor_token === $visitorToken) {
            return response()->file(
                storage_path('app/private/' . str_replace('private/', '', $message->file_path)),
                [
                    'Content-Type' => $message->file_type ?? 'application/octet-stream',
                    'Content-Disposition' => 'inline; filename="' . ($message->file_name ?? 'file') . '"',
                ]
            );
        }
    }

    abort(403, 'Unauthorized.');
})->name('chat.file');

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
        Route::post('switch-website', function (\Illuminate\Http\Request $request) {
            $websiteId = $request->input('website_id');

            if ($websiteId) {
                // Verify the website belongs to this user
                $valid = auth()->user()->websites()->where('id', $websiteId)->exists();
                abort_if(!$valid, 403, 'Unauthorized website.');
                session(['active_website_id' => (int) $websiteId]);
            } else {
                session()->forget('active_website_id');
            }

            // Return JSON for AJAX requests, redirect for normal
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'active_website_id' => $websiteId]);
            }

            return back();
        })->name('switch-website');

        // ── Notifications API ───────────────────────────────
        Route::get('notifications/unread-count', function () {
            $user = auth()->user();
            $allWebsiteIds = $user->websiteIds();

            // Count unread conversations per website + total
            $conversations = \App\Models\Conversation::with('website')
                ->whereIn('website_id', $allWebsiteIds)
                ->whereNotNull('visitor_id')
                ->whereHas('visitor')
                ->get();

            $totalUnread = 0;
            $perWebsite = [];

            foreach ($conversations as $conv) {
                $lastMsg = $conv->messages()->orderBy('id', 'desc')->first();
                $isVisitorMsg = $lastMsg && $lastMsg->sender_type === 'visitor';
                $isUnread = $isVisitorMsg
                    && $conv->last_message_at
                    && (!$conv->admin_viewed_at || $conv->last_message_at > $conv->admin_viewed_at);

                if ($isUnread) {
                    $totalUnread++;
                    $wid = $conv->website_id;
                    if (!isset($perWebsite[$wid])) {
                        $perWebsite[$wid] = ['count' => 0, 'name' => $conv->website->name ?? 'Unknown'];
                    }
                    $perWebsite[$wid]['count']++;
                }
            }

            return response()->json([
                'total' => $totalUnread,
                'per_website' => $perWebsite,
            ]);
        })->name('notifications.unread-count');

        Route::get('notifications/recent', function () {
            $user = auth()->user();
            $allWebsiteIds = $user->websiteIds();

            // Get most recent unread conversations across ALL websites
            $conversations = \App\Models\Conversation::with(['visitor', 'website'])
                ->whereIn('website_id', $allWebsiteIds)
                ->whereNotNull('visitor_id')
                ->whereHas('visitor')
                ->whereRaw('last_message_at > COALESCE(admin_viewed_at, "1970-01-01")')
                ->orderBy('last_message_at', 'desc')
                ->limit(10)
                ->get();

            $items = $conversations->map(function ($conv) {
                $lastMsg = $conv->messages()->orderBy('id', 'desc')->first();
                $isVisitorMsg = $lastMsg && $lastMsg->sender_type === 'visitor';

                if (!$isVisitorMsg) return null;

                return [
                    'conversation_id' => $conv->id,
                    'website_id'      => $conv->website_id,
                    'website_name'    => $conv->website->name ?? 'Unknown',
                    'visitor_label'   => $conv->visitor ? 'Visitor ' . substr($conv->visitor->visitor_token, 0, 8) : 'Unknown',
                    'message_preview' => \Illuminate\Support\Str::limit($lastMsg->message, 60),
                    'time_human'      => $conv->last_message_at?->diffForHumans(),
                    'time_iso'        => $conv->last_message_at?->toIso8601String(),
                ];
            })->filter()->values()->toArray();

            return response()->json($items);
        })->name('notifications.recent');

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