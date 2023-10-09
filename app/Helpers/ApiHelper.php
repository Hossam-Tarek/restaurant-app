<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiHelper
{

    /**
     * Return a JSON success response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function success(mixed $data = [], string $message = '', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Return a JSON failure response.
     *
     * @param mixed $errors
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function failure(mixed $errors = [], string $message = '', int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }
}
