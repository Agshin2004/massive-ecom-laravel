<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminProductsController;

Route::controller(AdminController::class)->group(function () {
    Route::post('seller-status', 'updateSellerStatus');
    Route::apiResource('manage-products', AdminProductsController::class);
});
