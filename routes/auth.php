<?php

use App\Http\Middleware\Auth\NotLoggedIn;
use App\Http\Controllers\Auth\AuthController;

Route::middleware([NotLoggedIn::class, 'throttle:api'])
    ->controller(AuthController::class)
    ->group(function () {
        Route::post('register-user', 'registerUser');
        Route::post('register-seller', 'registerSeller');
        Route::post('login', 'login');
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
    });
