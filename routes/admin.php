<?php

use App\Http\Controllers\Admin\AdminCategoriesController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminOrderItemController;
use App\Http\Controllers\Admin\AdminProductsController;

Route::controller(AdminController::class)->middleware(['isAdmin'])->group(function () {
    // TODO: make apiResource for seller status
    Route::post('seller-status', 'updateSellerStatus');

    Route::apiResource('manage-products', AdminProductsController::class);
    Route::apiResource('manage-categories', AdminCategoriesController::class);
    Route::apiResource('manage-orderItems', AdminOrderItemController::class);
});
