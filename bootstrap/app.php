<?php

use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
                    'message' => 'Not FOund',
                    'stack' => config('app.debug') ? $e->getTrace() : null
                ], 404);
            }

            if ($e instanceof TokenInvalidException) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Invalid Token'
                ], $e->getCode() ?? 400);
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
