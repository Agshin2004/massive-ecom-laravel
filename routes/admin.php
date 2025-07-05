<?php

use App\Http\Controllers\Admin\AdminController;

Route::controller(AdminController::class)->group(function () {
    Route::post('seller-status', 'updateSellerStatus');
});
