<?php

namespace App\Http\Traits;

trait ResponseTrait
{
    protected function successResponse($data = [], $message = 'success', $code = 200)
    {
        return response()->json([
            'message' => $message,
            'code' => $code,
            'payload' => $data
        ]);
    }

    protected function errorResponse($message = 'failed', $code)
    {
        return response()->json([
            'message' => $message,
            'code' => $code
        ]);
    }
}
