<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ViewController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', [ViewController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [ViewController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', [ViewController::class, 'dashboard'])->name('dashboard');
Route::get('/customers', [ViewController::class, 'customers'])->name('customers');
Route::get('/customers/{customer}', [ViewController::class, 'customerShow'])->name('customers.show');
Route::get('/orders', [ViewController::class, 'orders'])->name('orders');
Route::get('/orders/create', [ViewController::class, 'orderCreate'])->name('orders.create');
Route::get('/payments', [ViewController::class, 'payments'])->name('payments');
Route::get('/payments/create', [ViewController::class, 'paymentCreate'])->name('payments.create');
Route::get('/reports', [ViewController::class, 'reports'])->name('reports');
Route::get('/bottles', [ViewController::class, 'bottles'])->name('bottles');

Route::get('/debug-auth', function() {
    return response()->json([
        'auth_check' => \Illuminate\Support\Facades\Auth::check(),
        'user_id' => \Illuminate\Support\Facades\Auth::id(),
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
        'guard' => \Illuminate\Support\Facades\Auth::guard()->getName(),
    ]);
});

Route::get('/test-open', function() {
    return 'This page is open - no auth required';
});
