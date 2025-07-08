<?php

use Illuminate\Http\Request;
use App\Exceptions\ForbiddenException;
use App\Http\Middleware\Admin\isAdmin;
use Illuminate\Foundation\Application;
use App\Exceptions\UnauthorizedException;
use App\Http\Middleware\Auth\NotLoggedIn;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenBlacklistedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::prefix('api')
                ->group(function () {
                    //* auth routes
                    Route::prefix('auth')
                        ->group(base_path('routes/auth.php'));

                    //* users routes
                    Route::prefix('users')
                        ->group(base_path('routes/seller.php'));

                    //* seller routes
                    Route::prefix('seller')
                        ->group(base_path('routes/users.php'));

                    //* admin routes
                    Route::prefix('admin')
                        ->group(base_path('routes/admin.php'));
                });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'isAdmin' => isAdmin::class,
            'notLoggedIn' => NotLoggedIn::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (Throwable $e, Request $request) {
            if (!$request->is('api/*')) {
                // web errors will be rendered as usual
                return null;
            }

            if ($e instanceof ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Failed',
                    'errors' => $e->errors(),
                ], 422);  // could also use 400 bad request code but decided to use 422
            }

            if ($e instanceof NotFoundHttpException) {
                // error handle for handling 404
                return response()->json([
                    'success' => false,
                    'message' => config('app.debug') ? $e->getMessage() : 'Not Found',
                    'stack' => config('app.debug') ? $e->getTrace() : null,
                ], 404);
            }

            if ($e instanceof TokenBlacklistedException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token has been blacklisted',
                ], 401);
            }

            if ($e instanceof TokenInvalidException || $e instanceof JWTException) {
                return response()->json([
                    'success' => false,
                    'message' => "'Invalid or expired token'; {$e->getMessage()}",
                ], 401);
            }

            if ($e instanceof \Illuminate\Database\QueryException) {
                return response()->json([
                    'success' => false,
                    'message' => 'DB Error',
                    'stack' => config('app.debug') ? ($e->getMessage() ?: $e->getTrace()) : null,
                ], 400);
            }

            if ($e instanceof UnauthorizedException) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'stack' => config('app.debug') ? ($e->getMessage() ?: $e->getTrace()) : null,
                ], $e->getCode());
            }

            if ($e instanceof ForbiddenException) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'stack' => config('app.debug') ? ($e->getMessage() ?: $e->getTrace()) : null,
                ], $e->getCode());
            }

            // handle unhanled errors
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'stack' => config('app.debug') ? $e->getTrace() : null,
            ], $e->getCode() ?: 500);
        });
    })
    ->create();
