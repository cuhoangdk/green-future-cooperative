<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OrderController;

Route::get('/vnpay/checkout', [OrderController::class, 'showCheckoutForm'])->name('vnpay.checkout.form');
Route::post('/vnpay/checkout', [OrderController::class, 'create'])->name('vnpay.checkout');
Route::get('/vnpay-return', [OrderController::class, 'vnpayReturn'])->name('vnpay.return');