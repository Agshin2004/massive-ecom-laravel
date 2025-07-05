<?php

use App\Http\Middleware\SellerMiddleware;
use App\Http\Controllers\Seller\SellerController;

Route::middleware(SellerMiddleware::class)
    ->controller(SellerController::class)->group(function () {
        Route::post('order-status', 'updateOrderStatus');
    });
