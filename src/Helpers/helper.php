<?php

if (!function_exists('api_response')) {
    /**
     * Helper function to send a success response
     *
     * @param mixed $data
     * @param int $statusCode
     * @param string $message
     * @param array|null $meta
     * @return \Illuminate\Http\JsonResponse
     */
    function api_response($data = [], $message = 'Request was successful', $statusCode = 200, $meta = null)
    {
        return app('api-response')->success($data, $statusCode, $message, $meta);
    }
}

if (!function_exists('api_error')) {
    /**
     * Helper function to send an error response
     *
     * @param int $statusCode
     * @param string $message
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    function api_error($message = 'Something went wrong', $statusCode = 500, $errors = [])
    {
        return app('api-response')->error($statusCode, $message, $errors);
    }
}
