<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ── Widget API (public, authenticated by api_key) ──────────
Route::post('/chat/send', [ChatController::class, 'sendMessage']);
Route::post('/chat/send-file', [ChatController::class, 'sendMessage']); // Alias for multipart uploads
Route::post('/chat/messages', [ChatController::class, 'fetchMessages']);
Route::post('/chat/widget-config', [ChatController::class, 'widgetConfig']);

// ── Knowledge Base API (public, authenticated by api_key) ──
Route::post('/chat/categories', [ChatController::class, 'getCategories']);
Route::post('/chat/services', [ChatController::class, 'getServices']);
Route::post('/chat/sub-services', [ChatController::class, 'getSubServices']);
Route::post('/chat/sub-service-detail', [ChatController::class, 'getSubServiceDetail']);
