<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;

// Dashboard routes - all routes will be handled by Vue Router
Route::get('/{any}', [DashboardController::class, 'index'])
    ->where('any', '.*');
