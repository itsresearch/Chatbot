<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\ConversationController;
use App\Http\Controllers\Admin\DashboardController;

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

    Route::prefix('admin')->as('admin.')->group(function () {
            Route::redirect('/', '/admin/dashboard');

            Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
            
            Route::controller(ConversationController::class)->group(function () {

                Route::get('conversations', 'index')->name('conversations');
                Route::get('conversations/list', 'conversationsList')->name('conversations.list');

                Route::get('chat/{conversation}', 'show')->name('chat');
                Route::get('chat/{conversation}/content', 'chatContent')->name('chat.content');
                Route::post('chat/{conversation}/mark-viewed', 'markViewed')->name('chat.mark-viewed');
                Route::get('chat/{conversation}/messages', 'messages')->name('chat.messages');
                Route::post('chat/{conversation}/send', 'send')->name('chat.send');
            });

        });

});