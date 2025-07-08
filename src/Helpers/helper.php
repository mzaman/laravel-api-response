<?php

if (!function_exists('api_response')) {
    function api_response($data = [], $status = 'success', $code = 200, $message = '', $meta = null, $locale = null)
    {
        return response()->json(new \MasudZaman\LaravelApiResponse\Http\Resources\BaseResponse([
            'status' => $status,
            'code' => $code,
            'message' => $message ?: trans('messages.' . $status),
            'data' => $data,
            'meta' => $meta,
            'locale' => $locale,
        ]), $code);
    }
}

if (!function_exists('api_error')) {
    function api_error($message = 'Something went wrong', $status = 'error', $code = 500, $errors = null)
    {
        return response()->json(new \MasudZaman\LaravelApiResponse\Http\Resources\BaseResponse([
            'status' => $status,
            'code' => $code,
            'message' => $message,
            'errors' => $errors,
        ]), $code);
    }
}
