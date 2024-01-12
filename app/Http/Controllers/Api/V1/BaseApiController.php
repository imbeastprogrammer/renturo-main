<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseApiController extends Controller
{
    /**
     * Send a success JSON response.
     *
     * @param mixed  $data
     * @param string $message
     * @param int    $code
     * @return JsonResponse
     */
    protected function sendSuccessResponse($data, string $message = '', int $code = 200): JsonResponse
    {
        return response()->json([
            'message' => 'success',
            'body' => [
              'message' => $message,
                'data' => $data
            ]
        ], $code);
    }

    /**
     * Send an error JSON response.
     *
     * @param string $message
     * @param int    $code
     * @param array  $errors
     * @return JsonResponse
     */
    protected function sendFailedResponse(string $message, int $code = 404): JsonResponse
    {
        $response = [
            'message' => 'failed',
            'errors' => $message
        ];

        return response()->json($response, $code);
    }
}
