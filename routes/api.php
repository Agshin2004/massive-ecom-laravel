<?php

use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;

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

// TODO: Add auth middleware
Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('promos', PromoController::class);
    Route::apiResource('reviews', ReviewController::class);
    Route::apiResource('cart-items', CartController::class);

    Route::prefix('users')->group(function () {
        Route::get('reviews', [UserController::class, 'userReviews']);
        Route::get('reviews/{reviewId}', [UserController::class, 'userReviewById']);
        Route::get('cart-items', [UserController::class, 'userCartItems']);
    });
});
