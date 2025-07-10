<?php

namespace MasudZaman\LaravelApiResponse\Response;

use MasudZaman\LaravelApiResponse\Http\Resources\BaseResponse;
use MasudZaman\LaravelApiResponse\Support\HttpResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class ApiResponse
{
    /**
     * Send a successful response
     *
     * @param mixed $data
     * @param int $statusCode
     * @param string|null $message
     * @param array|null $meta
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($data = [], $statusCode = Response::HTTP_OK, $message = null, $meta = null, $headers = [])
    {
        $message = $message ?: HttpResponse::getMessage($statusCode);

        return response()->json(new BaseResponse([
            'status' => 'success',
            'code' => $statusCode,
            'message' => $message,
            'data' => $this->formatData($data),
            'meta' => $meta,
        ]), $statusCode, $headers);
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
     * @param string|null $message
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function error($statusCode = Response::HTTP_INTERNAL_SERVER_ERROR, $message = null, $errors = [], $headers = [])
    {
        $message = $message ?: HttpResponse::getMessage($statusCode);

        return response()->json(new BaseResponse([
            'status' => 'error',
            'code' => $statusCode,
            'message' => $message,
            'errors' => $errors,
        ]), $statusCode, $headers);
    }

    /**
     * Helper method to return standardized success response
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $statusCode
     * @param array|null $meta
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    private function sendResponse($data, $message, $statusCode, $meta = null, $headers = [])
    {
        $message = $message ?: HttpResponse::getMessage($statusCode);

        return response()->json(new BaseResponse([
            'status' => 'success',
            'code' => $statusCode,
            'message' => $message,
            'data' => $this->formatData($data),
            'meta' => $meta,
        ]), $statusCode, $headers);
    }

    /**
     * Helper method to return error response
     *
     * @param string|null $message
     * @param int $statusCode
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    private function sendError($message, $statusCode, $errors = [], $headers = [])
    {
        $message = $message ?: HttpResponse::getMessage($statusCode);

        return response()->json(new BaseResponse([
            'status' => 'error',
            'code' => $statusCode,
            'message' => $message,
            'errors' => $errors,
        ]), $statusCode, $headers);
    }

    /**
     * Send a resource created response (201)
     *
     * @param mixed $data
     * @param string|null $message
     * @param array|null $meta
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function created($data = [], $message = null, $meta = null, $headers = [])
    {
        return $this->sendResponse($data, $message, Response::HTTP_CREATED, $meta, $headers);
    }

    /**
     * Send an accepted response (202)
     *
     * @param mixed $data
     * @param string|null $message
     * @param array|null $meta
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function accepted($data = [], $message = null, $meta = null, $headers = [])
    {
        return $this->sendResponse($data, $message, Response::HTTP_ACCEPTED, $meta, $headers);
    }

    /**
     * Send a no content response (204)
     *
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function noContent($headers = [])
    {
        return response()->json(null, Response::HTTP_NO_CONTENT, $headers);
    }

    /**
     * Send a bad request response (400)
     *
     * @param string|null $message
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function badRequest($message = null, $errors = [], $headers = [])
    {
        return $this->sendError($message, Response::HTTP_BAD_REQUEST, $errors, $headers);
    }

    /**
     * Send an unauthorized response (401)
     *
     * @param string|null $message
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function unauthorized($message = null, $errors = [], $headers = [])
    {
        return $this->sendError($message, Response::HTTP_UNAUTHORIZED, $errors, $headers);
    }

    /**
     * Send a forbidden response (403)
     *
     * @param string|null $message
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function forbidden($message = null, $errors = [], $headers = [])
    {
        return $this->sendError($message, Response::HTTP_FORBIDDEN, $errors, $headers);
    }

    /**
     * Send a not found response (404)
     *
     * @param string|null $message
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function notFound($message = null, $errors = [], $headers = [])
    {
        return $this->sendError($message, Response::HTTP_NOT_FOUND, $errors, $headers);
    }

    /**
     * Send a validation error response (422)
     *
     * @param string|null $message
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function validationError($message = null, $errors = [], $headers = [])
    {
        return $this->sendError($message, Response::HTTP_UNPROCESSABLE_ENTITY, $errors, $headers);
    }

    /**
     * Send a too many requests response (429)
     *
     * @param string|null $message
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function tooManyRequests($message = null, $errors = [], $headers = [])
    {
        return $this->sendError($message, Response::HTTP_TOO_MANY_REQUESTS, $errors, $headers);
    }

    /**
     * Send an internal server error response (500)
     *
     * @param string|null $message
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function internalServerError($message = null, $errors = [], $headers = [])
    {
        return $this->sendError($message, Response::HTTP_INTERNAL_SERVER_ERROR, $errors, $headers);
    }

    /**
     * Send service unavailable response (503)
     *
     * @param string|null $message
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function serviceUnavailable($message = null, $errors = [], $headers = [])
    {
        return $this->sendError($message, Response::HTTP_SERVICE_UNAVAILABLE, $errors, $headers);
    }
}
