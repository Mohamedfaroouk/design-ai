<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Client\AiGenerationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Integration\SallaOAuthController;
use App\Http\Controllers\Integration\SallaWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    // Public routes (guest only)
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp']);

    // Protected routes (authenticated users)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'show']);
    Route::put('/', [ProfileController::class, 'update']);
    Route::put('/change-password', [ProfileController::class, 'changePassword']);
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    // Users Management
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('permission:users.view');
        Route::post('/', [UserController::class, 'store'])->middleware('permission:users.create');
        Route::get('/{id}', [UserController::class, 'show'])->middleware('permission:users.view');
        Route::put('/{id}', [UserController::class, 'update'])->middleware('permission:users.edit');
        Route::delete('/{id}', [UserController::class, 'destroy'])->middleware('permission:users.delete');
    });

    // Roles Management
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->middleware('permission:roles.view');
        Route::post('/', [RoleController::class, 'store'])->middleware('permission:roles.create');
        Route::get('/permissions', [RoleController::class, 'permissions'])->middleware('permission:roles.view');
        Route::get('/{id}', [RoleController::class, 'show'])->middleware('permission:roles.view');
        Route::put('/{id}', [RoleController::class, 'update'])->middleware('permission:roles.edit');
        Route::delete('/{id}', [RoleController::class, 'destroy'])->middleware('permission:roles.delete');
    });

    // Settings Management
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->middleware('permission:settings.view');
        Route::put('/batch', [SettingController::class, 'updateBatch'])->middleware('permission:settings.update');
    });

    // Stores Management
    Route::prefix('stores')->group(function () {
        Route::get('/', [StoreController::class, 'index'])->middleware('permission:stores.view');
        Route::get('/{id}', [StoreController::class, 'show'])->middleware('permission:stores.view');
        Route::put('/{id}', [StoreController::class, 'update'])->middleware('permission:stores.edit');
        Route::delete('/{id}', [StoreController::class, 'destroy'])->middleware('permission:stores.delete');
        Route::post('/{id}/refresh-token', [StoreController::class, 'refreshToken'])->middleware('permission:stores.edit');
    });

    // Packages Management
    Route::prefix('packages')->group(function () {
        Route::get('/', [PackageController::class, 'index'])->middleware('permission:packages.view');
        Route::get('/list', [PackageController::class, 'list'])->middleware('permission:packages.view');
        Route::post('/', [PackageController::class, 'store'])->middleware('permission:packages.create');
        Route::get('/{id}', [PackageController::class, 'show'])->middleware('permission:packages.view');
        Route::put('/{id}', [PackageController::class, 'update'])->middleware('permission:packages.edit');
        Route::delete('/{id}', [PackageController::class, 'destroy'])->middleware('permission:packages.delete');
    });
});

// Public settings endpoint
Route::get('/settings/public', [SettingController::class, 'public']);

// Public packages endpoint (for pricing page)
Route::get('/packages', [PackageController::class, 'list']);
Route::get('/packages/platform/{platform}', [PackageController::class, 'list']);

/*
|--------------------------------------------------------------------------
| Store Integration Routes (Public - No Auth)
|--------------------------------------------------------------------------
*/

// Salla OAuth
Route::prefix('integration/salla')->group(function () {
    Route::get('/authorize', [SallaOAuthController::class, 'redirect']);
    Route::get('/callback', [SallaOAuthController::class, 'callback']);
});

// Salla Webhooks
Route::post('/webhooks/salla', [SallaWebhookController::class, 'handle']);

// TODO: Add Zid and WordPress integration routes when implemented
// Route::prefix('integration/zid')->group(function () { ... });
// Route::prefix('integration/wordpress')->group(function () { ... });

/*
|--------------------------------------------------------------------------
| Client Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->prefix('client')->group(function () {
    // Upload endpoint
    Route::post('/uploads', [App\Http\Controllers\Client\UploadController::class, 'upload']);

    // AI Image Generation
    Route::prefix('ai')->name('client.ai.')->group(function () {
        Route::post('/generate', [App\Http\Controllers\Client\AIImageController::class, 'generate']);
        Route::get('/status/{jobId}', [App\Http\Controllers\Client\AIImageController::class, 'status']);
        Route::post('/callback', [App\Http\Controllers\Client\AIImageController::class, 'callback'])->name('callback')->withoutMiddleware('auth:sanctum');
    });

    // AI Generation Jobs (no permissions required - clients access their own jobs only)
    Route::prefix('ai-generations')->group(function () {
        Route::get('/', [AiGenerationController::class, 'index']);
        Route::post('/{aiGenerationJob}/retry', [AiGenerationController::class, 'retry']);
        Route::get('/{aiGenerationJob}/download', [AiGenerationController::class, 'download']);
    });
});

// Upload endpoint (example - legacy)
Route::post('/upload/avatar', function (Request $request) {
    if ($request->hasFile('file')) {
        $path = $request->file('file')->store('avatars', 'public');
        return response()->json([
            'url' => asset('storage/' . $path),
            'path' => $path
        ]);
    }

    return response()->json(['error' => 'No file uploaded'], 400);
});
