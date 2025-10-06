<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\ProfileController;
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
});

// Public settings endpoint
Route::get('/settings/public', [SettingController::class, 'public']);

// Upload endpoint (example)
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
