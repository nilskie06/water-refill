<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ViewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [ViewController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [ViewController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->middleware('session.auth')->name('logout');

Route::middleware('session.auth')->group(function () {
    Route::get('/dashboard', [ViewController::class, 'dashboard'])->name('dashboard');
    Route::get('/customers', [ViewController::class, 'customers'])->name('customers');
    Route::get('/customers/{customer}', [ViewController::class, 'customerShow'])->name('customers.show');
    Route::get('/orders', [ViewController::class, 'orders'])->name('orders');
    Route::get('/orders/create', [ViewController::class, 'orderCreate'])->name('orders.create');
    Route::get('/payments', [ViewController::class, 'payments'])->name('payments');
    Route::get('/payments/create', [ViewController::class, 'paymentCreate'])->name('payments.create');
    Route::get('/reports', [ViewController::class, 'reports'])->name('reports');
    Route::get('/bottles', [ViewController::class, 'bottles'])->name('bottles');
});
