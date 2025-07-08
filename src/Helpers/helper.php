<?php

if (!function_exists('api_response')) {
    function api_response($data = [], $status = 1000, $httpStatus = 200, $message = 'Operation successful', $meta = null, $locale = null)
    {
        return response()->json(new \mzaman\LaravelApiResponse\Http\Resources\BaseResponse([
            'status' => $status,
            'httpStatus' => $httpStatus,
            'success' => $httpStatus < 400,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
            'locale' => $locale,
        ]), $httpStatus);
    }
}

if (!function_exists('api_error')) {
    function api_error($message = 'Something went wrong', $status = 1006, $httpStatus = 500, $errors = null)
    {
        return response()->json(new \mzaman\LaravelApiResponse\Http\Resources\BaseResponse([
            'status' => $status,
            'httpStatus' => $httpStatus,
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ]), $httpStatus);
    }
}
