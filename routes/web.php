<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\ConversationController;


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
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    Route::get('/admin/conversations', [ConversationController::class, 'index'])->name('admin.conversations');
    
    Route::get('/admin/chat/{conversation}', [ConversationController::class, 'show'])->name('admin.chat');
    Route::get('/admin/chat/{conversation}/messages', [ConversationController::class, 'messages'])->name('admin.chat.messages');
    Route::post('/admin/chat/{conversation}/send', [ConversationController::class, 'send'])->name('admin.chat.send');
});

