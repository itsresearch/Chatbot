<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ── Widget API (public, authenticated by api_key) ──────────
Route::post('/chat/send', [ChatController::class, 'sendMessage']);
Route::post('/chat/messages', [ChatController::class, 'fetchMessages']);
Route::post('/chat/widget-config', [ChatController::class, 'widgetConfig']);
