<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderProductController;
use App\Http\Controllers\ProductController; 
use Illuminate\Support\Facades\Route;

Route::middleware([\App\Http\Middleware\EnsureTokenIsValid::class])->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/orders', [OrderController::class, 'getOpenOrder']);
    Route::get('/orders/completed', [OrderController::class, 'listCompletedOrders']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/order-products', [OrderProductController::class, 'listByOrder']);
    Route::post('/order-products', [OrderProductController::class, 'store']);
});