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
     * @param int $code
     * @param string|null $message
     * @param array|null $meta
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($data = [], $code = Response::HTTP_OK, $message = null, $meta = null, $headers = [])
    {
        // Set default message if not provided
        $message = $message ?: HttpResponse::getMessage($code);

        return response()->json(new BaseResponse([
            'status' => HttpResponse::getType($code),
            'code' => $code,
            'message' => $message,
            'data' => $this->formatData($data),
            'meta' => $meta,
        ]), $code, $headers);
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
     * @param int $code
     * @param string|null $message
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function error($code = Response::HTTP_INTERNAL_SERVER_ERROR, $message = null, $errors = [], $headers = [])
    {
        // Set default message if not provided
        $message = $message ?: HttpResponse::getMessage($code);

        return response()->json(new BaseResponse([
            'status' => HttpResponse::getType($code),
            'code' => $code,
            'message' => $message,
            'errors' => $errors,
        ]), $code, $headers);
    }

    /**
     * Helper method to return standardized success response
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $code
     * @param array|null $meta
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    private function sendResponse($data, $message, $code = Response::HTTP_OK, $meta = null, $headers = [])
    {
        $message = $message ?: HttpResponse::getMessage($code);

        return response()->json(new BaseResponse([
            'status' => HttpResponse::getType($code),
            'code' => $code,
            'message' => $message,
            'data' => $this->formatData($data),
            'meta' => $meta,
        ]), $code, $headers);
    }

    /**
     * Helper method to return error response
     *
     * @param string|null $message
     * @param int $code
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    private function sendError($message, $code = Response::HTTP_INTERNAL_SERVER_ERROR, $errors = [], $headers = [])
    {
        $message = $message ?: HttpResponse::getMessage($code);

        return response()->json(new BaseResponse([
            'status' => HttpResponse::getType($code),
            'code' => $code,
            'message' => $message,
            'errors' => $errors,
        ]), $code, $headers);
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
     * Send a resource updated response (200)
     *
     * @param mixed $data
     * @param string|null $message
     * @param array|null $meta
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function updated($data = [], $message = null, $meta = null, $headers = [])
    {
        return $this->sendResponse($data, $message, Response::HTTP_OK, $meta, $headers);
    }

    /**
     * Send a resource deleted response (200)
     *
     * @param string|null $message
     * @param array|null $meta
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleted($message = null, $meta = null, $headers = [])
    {
        return $this->sendResponse(null, $message ?: HttpResponse::getMessage(Response::HTTP_OK), Response::HTTP_OK, $meta, $headers);
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
    public function manyRequests($message = null, $errors = [], $headers = [])
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

    /**
     * Send a not acceptable response (406)
     *
     * @param string|null $message
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function notAcceptable($message = null, $errors = [], $headers = [])
    {
        return $this->sendError($message, Response::HTTP_NOT_ACCEPTABLE, $errors, $headers);
    }

    /**
     * Send a conflict response (409)
     *
     * @param string|null $message
     * @param array|null $errors
     * @param array|null $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function conflict($message = null, $errors = [], $headers = [])
    {
        return $this->sendError($message, Response::HTTP_CONFLICT, $errors, $headers);
    }
}
