<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public function response($success = true, $message = null, $data = null, $code = 200, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'code' => $code,
            'errors' => $errors,
        ], $code);
    }
}
