<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/vnpay/checkout', [OrderController::class, 'showCheckoutForm'])->name('vnpay.checkout.form')->middleware('customer.auth');
Route::post('/vnpay/checkout', [OrderController::class, 'create'])->name('vnpay.checkout')->middleware('customer.auth');
Route::get('/vnpay-return', [OrderController::class, 'vnpayReturn'])->name('vnpay.return')->middleware('customer.auth');