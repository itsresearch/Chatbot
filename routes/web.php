<?php

use Illuminate\Support\Facades\Route;

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
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    Route::get('/admin/conversations', function () {
        return view('admin.conversations');
    })->name('admin.conversations');
    
    Route::get('/admin/chat/{id?}', function ($id = null) {
        return view('admin.chat');
    })->name('admin.chat');
});
