<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\WebAuthController;
use App\Http\Controllers\Admin\WebUserController;
use App\Http\Controllers\Admin\WebRoleController;
use App\Http\Controllers\Admin\WebSettingController;
use App\Http\Controllers\Client\WebProductController;
use App\Http\Controllers\Client\WebAIImageController;

// Test route - verify Inertia is working
Route::get('/test-inertia', function () {
    return Inertia::render('TestPage', [
        'message' => 'Inertia is working!'
    ]);
});

// Guest routes (Authentication)
Route::middleware('guest')->group(function () {
    Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login']);

    Route::get('/forgot-password', [WebAuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [WebAuthController::class, 'forgotPassword'])->name('password.email');

    Route::get('/verify-otp', [WebAuthController::class, 'showVerifyOtp'])->name('auth.otp.verify');
    Route::post('/verify-otp', [WebAuthController::class, 'verifyOtp'])->name('auth.otp.check');

    Route::get('/reset-password', [WebAuthController::class, 'showResetPassword'])->name('auth.password.reset');
    Route::post('/reset-password', [WebAuthController::class, 'resetPassword'])->name('password.update');

    Route::post('/resend-otp', [WebAuthController::class, 'resendOtp'])->name('auth.otp.resend');
});
// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

    // Dashboard placeholder
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');

        Route::middleware('permission:users.view')->group(function () {
            Route::resource('users', WebUserController::class);
        });

        Route::middleware('permission:roles.view')->group(function () {
            Route::resource('roles', WebRoleController::class);
        });

        Route::middleware('permission:settings.view')->group(function () {
            Route::get('settings', [WebSettingController::class, 'index'])->name('settings.index');
            Route::put('settings', [WebSettingController::class, 'update'])->name('settings.update');
        });
    });

    // Client routes
    Route::prefix('client')->name('client.')->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');

        // Products
        Route::resource('products', WebProductController::class);

        // AI Image Generation
        Route::get('/ai-generations', [WebAIImageController::class, 'index'])->name('ai.index');
        Route::get('/ai-generations/wizard', [WebAIImageController::class, 'wizard'])->name('ai.wizard');
        Route::get('/ai-image-generator', [WebAIImageController::class, 'wizard'])->name('ai.generator'); // Alias for wizard
        Route::post('/ai-generations/generate', [WebAIImageController::class, 'generate'])->name('ai.generate');
        Route::get('/ai-generations/status/{jobId}', [WebAIImageController::class, 'status'])->name('ai.status');
    });
});

// AI Callback (external, no auth required)
Route::post('/ai-generation/callback', [WebAIImageController::class, 'callback'])->name('ai.callback');

// Redirect root to login or dashboard
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});
