<?php

use Illuminate\Support\Facades\Route;
use Nk\SystemAuth\Http\Controllers\AuthController;

// Route::group(['middleware' => ['web']], function () {
    Route::get('/key', [AuthController::class, 'showKeyPage'])->name('system.auth.key');
    Route::post('/key', [AuthController::class, 'verifyKey'])->name('system.auth.verify');
    Route::get('/login', [AuthController::class, 'showLoginPage'])->name('system.auth.login')->middleware('key.verified');
    // Route::get('/login', [AuthController::class, 'showLoginPage'])->name('system.auth.login');
    Route::post('/login', [AuthController::class, 'login'])->name('system.auth.login.post');
    
// });