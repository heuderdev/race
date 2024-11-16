<?php

namespace App\Helper;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Function : Common function to display success - JSON Response
     * @param string $status
     * @param string|null $message
     * @param array $data
     * @param integer $statusCode
     * @return JsonResponse
     */
    public static function success(string $status = 'success', string $message = null, array $data = [], int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Function : Common function to display error - JSON Response
     * @param string $status
     * @param string|null $message
     * @param integer $statusCode
     * @return JsonResponse
     */
    public static function error(string $status = 'error', string $message = null, int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
        ], $statusCode);
    }
}
