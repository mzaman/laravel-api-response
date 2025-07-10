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

if (!function_exists('response_success')) {
    /**
     * Helper function to send a basic success response (200)
     *
     * @param mixed $data
     * @param string $message
     * @param array|null $meta
     * @return \Illuminate\Http\JsonResponse
     */
    function response_success($data = [], $message = 'Request was successful', $meta = null)
    {
        return app('api-response')->response_success($data, $message, $meta);
    }
}

if (!function_exists('response_created')) {
    /**
     * Helper function to send a resource created response (201)
     *
     * @param mixed $data
     * @param string $message
     * @param array|null $meta
     * @return \Illuminate\Http\JsonResponse
     */
    function response_created($data = [], $message = 'Resource created successfully', $meta = null)
    {
        return app('api-response')->response_created($data, $message, $meta);
    }
}

if (!function_exists('response_accepted')) {
    /**
     * Helper function to send an accepted response (202)
     *
     * @param mixed $data
     * @param string $message
     * @param array|null $meta
     * @return \Illuminate\Http\JsonResponse
     */
    function response_accepted($data = [], $message = 'Request accepted for processing', $meta = null)
    {
        return app('api-response')->response_accepted($data, $message, $meta);
    }
}

if (!function_exists('response_no_content')) {
    /**
     * Helper function to send a no content response (204)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function response_no_content()
    {
        return app('api-response')->response_no_content();
    }
}

if (!function_exists('response_error')) {
    /**
     * Helper function to send a server error response (500)
     *
     * @param string $message
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    function response_error($message = 'Internal Server Error', $errors = null)
    {
        return app('api-response')->response_error($message, $errors);
    }
}

if (!function_exists('response_unavailable')) {
    /**
     * Helper function to send service unavailable response (503)
     *
     * @param string $message
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    function response_unavailable($message = 'Service temporarily unavailable', $errors = null)
    {
        return app('api-response')->response_unavailable($message, $errors);
    }
}

if (!function_exists('response_maintenance')) {
    /**
     * Helper function to send maintenance mode response (503)
     *
     * @param string $message
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    function response_maintenance($message = 'Service under maintenance', $errors = null)
    {
        return app('api-response')->response_maintenance($message, $errors);
    }
}

if (!function_exists('response_fail')) {
    /**
     * Helper function to send a bad request response (400)
     *
     * @param string $message
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    function response_fail($message = 'Bad Request', $errors = null)
    {
        return app('api-response')->response_fail($message, $errors);
    }
}

if (!function_exists('response_unauthorized')) {
    /**
     * Helper function to send an unauthorized response (401)
     *
     * @param string $message
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    function response_unauthorized($message = 'Unauthorized', $errors = null)
    {
        return app('api-response')->response_unauthorized($message, $errors);
    }
}

if (!function_exists('response_forbidden')) {
    /**
     * Helper function to send a forbidden response (403)
     *
     * @param string $message
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    function response_forbidden($message = 'Forbidden', $errors = null)
    {
        return app('api-response')->response_forbidden($message, $errors);
    }
}

if (!function_exists('response_not_found')) {
    /**
     * Helper function to send a not found response (404)
     *
     * @param string $message
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    function response_not_found($message = 'Not Found', $errors = null)
    {
        return app('api-response')->response_not_found($message, $errors);
    }
}

if (!function_exists('response_validation')) {
    /**
     * Helper function to send a validation error response (422)
     *
     * @param string $message
     * @param array $errors
     * @return \Illuminate\Http\JsonResponse
     */
    function response_validation($message = 'Validation Error', $errors = [])
    {
        return app('api-response')->response_validation($message, $errors);
    }
}

if (!function_exists('response_too_many_requests')) {
    /**
     * Helper function to send a too many requests response (429)
     *
     * @param string $message
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    function response_too_many_requests($message = 'Too Many Requests', $errors = null)
    {
        return app('api-response')->response_too_many_requests($message, $errors);
    }
}

if (! function_exists('validation_exception')) {
    /**
     * Helper function to create a structured validation exception response
     *
     * @param  string  $message
     * @param  int  $code
     * @param  array  $errors
     * @return MasudZaman\LaravelApiResponse\Exceptions\ValidationException
     */
    function validation_exception($message, $code = 400, $errors = [])
    {
        $errorResponse = [
            'success' => false,
            'status' => 'error',
            'code' => $code,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
            'meta' => null,
            'locale' => app()->getLocale(),
        ];

        // Return the custom ValidationException with structured response
        throw new \MasudZaman\LaravelApiResponse\Exceptions\ValidationException($errorResponse);
    }
}

