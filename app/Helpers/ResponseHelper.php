<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    /**
     * Standard success response.
     */
    public static function success(
        string $message = 'Success',
        mixed $data = null,
        int $statusCode = 200
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Standard error response.
     */
    public static function error(
        string $message = 'Error',
        mixed $errors = null,
        int $statusCode = 400
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Validation error response.
     */
    public static function validationError(
        string $message = 'Validation failed',
        mixed $errors = null
    ): JsonResponse {
        return self::error($message, $errors, 422);
    }

    /**
     * Not found error response.
     */
    public static function notFound(
        string $message = 'Resource not found'
    ): JsonResponse {
        return self::error($message, null, 404);
    }

    /**
     * Unauthorized response.
     */
    public static function unauthorized(
        string $message = 'Unauthorized'
    ): JsonResponse {
        return self::error($message, null, 401);
    }

    /**
     * Forbidden response.
     */
    public static function forbidden(
        string $message = 'Forbidden'
    ): JsonResponse {
        return self::error($message, null, 403);
    }
}
