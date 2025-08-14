<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BroadcastingController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', LoginController::class)->name('login.post');

// Dashboard route (protected by auth middleware)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Broadcasting routes (protected by auth middleware)
Route::get('/broadcasting', [BroadcastingController::class, 'index'])->middleware('auth')->name('broadcasting');

// Broadcasting API routes
Route::middleware('auth')->group(function () {
    Route::post('/broadcasting/start', [BroadcastingController::class, 'startStream'])->name('broadcasting.start');
    Route::post('/broadcasting/stop', [BroadcastingController::class, 'stopStream'])->name('broadcasting.stop');
    Route::post('/broadcasting/message', [BroadcastingController::class, 'sendMessage'])->name('broadcasting.message');
    Route::get('/broadcasting/messages', [BroadcastingController::class, 'getMessages'])->name('broadcasting.messages');
    Route::post('/broadcasting/viewers', [BroadcastingController::class, 'updateViewerCount'])->name('broadcasting.viewers');
    Route::get('/broadcasting/info', [BroadcastingController::class, 'getStreamInfo'])->name('broadcasting.info');
    Route::post('/broadcasting/interaction', [BroadcastingController::class, 'handleInteraction'])->name('broadcasting.interaction');
    Route::get('/broadcasting/cameras', [BroadcastingController::class, 'getCameras'])->name('broadcasting.cameras');
});

// Logout route
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->middleware('auth')->name('logout');
