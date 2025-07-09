<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\OrderController;
use App\Http\Controllers\Shop\PromoController;
use App\Http\Controllers\Shop\ReviewController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\CategoryController;

Route::middleware(['auth:api', 'throttle:api'])->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('promos', PromoController::class);
    Route::apiResource('reviews', ReviewController::class);
    Route::apiResource('cart-items', CartController::class);
    Route::apiResource('orders', OrderController::class);
});
