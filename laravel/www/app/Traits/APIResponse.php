<?php

namespace App\Traits;

trait APIResponse
{
//    public function successResponse()
//    {
//        return response()->json([
//            'success' => true,
//            'message' => 'Operation Successful',
//        ], 200);
//    }

    public function successResponseWithMessage($message)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
        ], 200);
    }

    public function successResponseWithData($data, $message = 'Operation Successful')
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], 200);
    }

    public function failureResponse($status_code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => 'Operation Failure',
        ], $status_code);
    }

    public function failureResponseWithMessage($message, $status_code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status_code);
    }

    public function validationErrorResponse($validator, $status_code = 400, $message = 'Validation failed!')
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $validator->errors(),
        ], $status_code);
    }

    public function authFailedResponse($message = 'Unauthorized', $status_code = 401)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status_code);
    }

    public function errorResponse(string $message, int $status_code = 400, $errors = []): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'FAILED',
            'status_code' => $status_code,
            'message' => $message,
            'errors' => $errors,
        ], $status_code);
    }

    public function successResponse($message, $data = [], $status_code = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'SUCCESS',
            'status_code' => $status_code,
            'message' => $message,
            'data' => $data,
        ], $status_code);
    }
}
