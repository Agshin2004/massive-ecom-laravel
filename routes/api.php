<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PromoController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// * Auth Related Routes
Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('register-user', 'registerUser');
    Route::post('register-seller', 'registerSeller');
    Route::post('login', 'login');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

// * Product Related Routes
Route::apiResource('product', ProductController::class);
Route::apiResource('category', CategoryController::class);
Route::apiResource('promo', PromoController::class);
