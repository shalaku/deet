<?php

if (! function_exists('responseJson')) {
    function responseJson($success = true, $message = '', $data = [], $status_code = 200)
    {
        $response['success'] = $success;

        if (! empty($message)) {
            $response['message'] = $message;
        }

        if (count($data) > 0) {
            if ($status_code == 422) {
                $response['errors'] = $data;
            } else {
                $response['data'] = $data;
            }
        }

        $status_code = ($status_code >= 100 && $status_code < 600) ? $status_code : 500;

        return response()->json($response, $status_code);
    }
}
