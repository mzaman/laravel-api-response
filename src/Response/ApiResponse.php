<?php

namespace MasudZaman\LaravelApiResponse\Response;

use MasudZaman\LaravelApiResponse\Http\Resources\BaseResponse;
use Illuminate\Support\Collection;

class ApiResponse
{
    /**
     * Send a successful response
     *
     * @param mixed $data
     * @param int $statusCode
     * @param string $message
     * @param array|null $meta
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($data = [], $statusCode = 200, $message = 'Request was successful', $meta = null)
    {
        return response()->json(new BaseResponse([
            'status' => 'success',
            'code' => $statusCode,
            'message' => $message,
            'data' => $this->formatData($data),
            'meta' => $meta,
        ]), $statusCode);
    }

    /**
     * Format data for consistency, handling pagination if needed.
     *
     * @param mixed $data
     * @return mixed
     */
    private function formatData($data)
    {
        if ($data instanceof Collection) {
            // Handle paginated data (e.g., from Eloquent)
            return $data->toArray();
        }

        return $data;
    }

    /**
     * Send an error response
     *
     * @param int $statusCode
     * @param string $message
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function error($statusCode = 500, $message = 'Something went wrong',$errors = [])
    {
        return response()->json(new BaseResponse([
            'status' => 'error',
            'code' => $statusCode,
            'message' => $message,
            'errors' => $errors,
        ]), $statusCode);
    }

    /**
     * Send a basic success response (200)
     *
     * @param mixed $data
     * @param string $message
     * @param array|null $meta
     * @return \Illuminate\Http\JsonResponse
     */
    public function response_success($data = [], $message = 'Request was successful', $meta = null)
    {
        return $this->sendResponse($data, $message, 200, $meta);
    }

    /**
     * Send a resource created response (201)
     *
     * @param mixed $data
     * @param string $message
     * @param array|null $meta
     * @return \Illuminate\Http\JsonResponse
     */
    public function response_created($data = [], $message = 'Resource created successfully', $meta = null)
    {
        return $this->sendResponse($data, $message, 201, $meta);
    }

    /**
     * Send an accepted response (202)
     *
     * @param mixed $data
     * @param string $message
     * @param array|null $meta
     * @return \Illuminate\Http\JsonResponse
     */
    public function response_accepted($data = [], $message = 'Request accepted for processing', $meta = null)
    {
        return $this->sendResponse($data, $message, 202, $meta);
    }

    /**
     * Send a no content response (204)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function response_no_content()
    {
        return response()->json(null, 204);
    }

    /**
     * Send an error response (500)
     *
     * @param string $message
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function response_error($message = 'Internal Server Error', $errors = null)
    {
        return $this->sendError($message, 500, $errors);
    }

    /**
     * Send service unavailable response (503)
     *
     * @param string $message
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function response_unavailable($message = 'Service temporarily unavailable', $errors = null)
    {
        return $this->sendError($message, 503, $errors);
    }

    /**
     * Send maintenance mode response (503)
     *
     * @param string $message
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function response_maintenance($message = 'Service under maintenance', $errors = null)
    {
        return $this->sendError($message, 503, $errors);
    }

    /**
     * Send a bad request response (400)
     *
     * @param string $message
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function response_fail($message = 'Bad Request', $errors = null)
    {
        return $this->sendError($message, 400, $errors);
    }

    /**
     * Send an unauthorized response (401)
     *
     * @param string $message
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function response_unauthorized($message = 'Unauthorized', $errors = null)
    {
        return $this->sendError($message, 401, $errors);
    }

    /**
     * Send a forbidden response (403)
     *
     * @param string $message
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function response_forbidden($message = 'Forbidden', $errors = null)
    {
        return $this->sendError($message, 403, $errors);
    }

    /**
     * Send a not found response (404)
     *
     * @param string $message
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function response_not_found($message = 'Not Found', $errors = null)
    {
        return $this->sendError($message, 404, $errors);
    }

    /**
     * Send a validation error response (422)
     *
     * @param string $message
     * @param array $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function response_validation($message = 'Validation Error', $errors = [])
    {
        return $this->sendError($message, 422, $errors);
    }

    /**
     * Send a too many requests response (429)
     *
     * @param string $message
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function response_too_many_requests($message = 'Too Many Requests', $errors = null)
    {
        return $this->sendError($message, 429, $errors);
    }

    /**
     * Helper method to return standardized response
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @param array|null $meta
     * @return \Illuminate\Http\JsonResponse
     */
    private function sendResponse($data, $message, $statusCode, $meta = null)
    {
        return response()->json(new BaseResponse([
            'status' => 'success',
            'code' => $statusCode,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
        ]), $statusCode);
    }

    /**
     * Helper method to return error response
     *
     * @param string $message
     * @param int $statusCode
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    private function sendError($message, $statusCode, $errors = null)
    {
        return response()->json(new BaseResponse([
            'status' => 'error',
            'code' => $statusCode,
            'message' => $message,
            'errors' => $errors,
        ]), $statusCode);
    }
}