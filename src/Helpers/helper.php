<?php

if (!function_exists('apiResponse')) {
    /**
     * Helper function to send a success response
     *
     * @param mixed $data
     * @param int $code
     * @param string|null $message
     * @param array|null $meta
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    function apiResponse($data = [], $message = null, $code = Response::HTTP_OK, $meta = null, $headers = [])
    {
        return app('api-response')->success($data, $code, $message, $meta, $headers);
    }
}

if (!function_exists('errorResponse')) {
    /**
     * Helper function to send an error response
     *
     * @param int $code
     * @param string|null $message
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    function errorResponse($message = null, $code = Response::HTTP_INTERNAL_SERVER_ERROR, $errors = [], $headers = [])
    {
        return app('api-response')->error($code, $message, $errors, $headers);
    }
}

if (!function_exists('successResponse')) {
    /**
     * Helper function to send a basic success response (200)
     *
     * @param mixed $data
     * @param string|null $message
     * @param array|null $meta
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    function successResponse($data = [], $message = null, $meta = null, $headers = [])
    {
        return app('api-response')->success($data, Response::HTTP_OK, $message, $meta, $headers);
    }
}

if (!function_exists('createdResponse')) {
    /**
     * Helper function to send a resource created response (201)
     *
     * @param mixed $data
     * @param string|null $message
     * @param array|null $meta
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    function createdResponse($data = [], $message = null, $meta = null, $headers = [])
    {
        return app('api-response')->created($data, $message, $meta, $headers);
    }
}

if (!function_exists('acceptedResponse')) {
    /**
     * Helper function to send an accepted response (202)
     *
     * @param mixed $data
     * @param string|null $message
     * @param array|null $meta
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    function acceptedResponse($data = [], $message = null, $meta = null, $headers = [])
    {
        return app('api-response')->accepted($data, $message, $meta, $headers);
    }
}

if (!function_exists('noContentResponse')) {
    /**
     * Helper function to send a no content response (204)
     *
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    function noContentResponse($headers = [])
    {
        return app('api-response')->noContent($headers);
    }
}

if (!function_exists('errorResponse')) {
    /**
     * Helper function to send a server error response (500)
     *
     * @param string|null $message
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    function errorResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->error(Response::HTTP_INTERNAL_SERVER_ERROR, $message, $errors, $headers);
    }
}

if (!function_exists('unavailableResponse')) {
    /**
     * Helper function to send service unavailable response (503)
     *
     * @param string|null $message
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    function unavailableResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->serviceUnavailable($message, $errors, $headers);
    }
}

if (!function_exists('maintenanceResponse')) {
    /**
     * Helper function to send maintenance mode response (503)
     *
     * @param string|null $message
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    function maintenanceResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->serviceUnavailable($message, $errors, $headers);
    }
}

if (!function_exists('failResponse')) {
    /**
     * Helper function to send a bad request response (400)
     *
     * @param string|null $message
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    function failResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->badRequest($message, $errors, $headers);
    }
}

if (!function_exists('unauthorizedResponse')) {
    /**
     * Helper function to send an unauthorized response (401)
     *
     * @param string|null $message
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    function unauthorizedResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->unauthorized($message, $errors, $headers);
    }
}

if (!function_exists('forbiddenResponse')) {
    /**
     * Helper function to send a forbidden response (403)
     *
     * @param string|null $message
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    function forbiddenResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->forbidden($message, $errors, $headers);
    }
}

if (!function_exists('notFoundResponse')) {
    /**
     * Helper function to send a not found response (404)
     *
     * @param string|null $message
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    function notFoundResponse($message = null, $errors = null, $headers = [])
    {
        return app('api-response')->notFound($message, $errors, $headers);
    }
}

if (!function_exists('validationErrorResponse')) {
    /**
     * Helper function to send a validation error response (422)
     *
     * @param string|null $message
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    function validationErrorResponse($message = null, $errors = [], $headers = [])
    {
        return app('api-response')->validationError($message, $errors, $headers);
    }
}

if (!function_exists('manyRequestsResponse')) {
    /**
     * Helper function to send a too many requests response (429)
     *
     * @param string|null $message
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    function manyRequestsResponse($message = null, $errors = [], $headers = [])
    {
        return app('api-response')->tooManyRequests($message, $errors, $headers);
    }
}
