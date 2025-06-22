<?php

use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenBlacklistedException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
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
                    'errors' => $e->errors()
                ], 422);  // could also use 400 bad request code but decided to use 422
            }

            if ($e instanceof NotFoundHttpException) {
                // error handle for handling 404
                return response()->json([
                    'success' => false,
                    'message' => 'Not Found',
                    'stack' => config('app.debug') ? $e->getTrace() : null
                ], 404);
            }

            if ($e instanceof TokenBlacklistedException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token has been blacklisted'
                ], 401);
            }

            if ($e instanceof TokenInvalidException || $e instanceof JWTException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired token'
                ], 401);
            }

            if ($e instanceof \Illuminate\Database\QueryException) {
                return response()->json([
                    'success' => false,
                    'message' => 'DB Error',
                    'stack' => config('app.debug') ? ($e->getMessage() ?: $e->getTrace()) : null
                ], 400);
            }

            // handle unhanled errors
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'stack' => config('app.debug') ? $e->getTrace() : null
            ], $e->getCode() ?: 500);
        });
    })
    ->create();
