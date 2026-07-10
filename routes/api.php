<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes - use session auth for web requests
Route::middleware('session.auth')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Customers (all authenticated users)
    Route::apiResource('customers', CustomerController::class);

    // Orders (all authenticated users)
    Route::apiResource('orders', OrderController::class);

    // Payments (all authenticated users)
    Route::apiResource('payments', PaymentController::class)->only(['index', 'store', 'show', 'destroy']);

    // Reports (admin only)
    Route::middleware('admin')->group(function () {
        Route::get('/reports/daily-sales', [ReportController::class, 'dailySales']);
    });
});
