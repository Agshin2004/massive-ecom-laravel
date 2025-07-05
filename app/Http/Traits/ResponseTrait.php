<?php

namespace App\Http\Traits;

trait ResponseTrait
{
    protected function successResponse($data = [], $message = 'success', $code = 200)
    {
        return response()->json([
            'message' => $message,
            'code' => $code,
            'payload' => $data,
        ], $code);
    }

    protected function errorResponse($message = 'failed', $code = 400)
    {
        return response()->json([
            'message' => $message,
            'code' => $code,
        ], $code);
    }

    protected function created($message = 'created', $code = 201)
    {
        return response()->json([
            'message' => $message,
            'code' => $code,
        ], $code);
    }

    protected function noContent()
    {
        return response()->noContent();
    }
}
