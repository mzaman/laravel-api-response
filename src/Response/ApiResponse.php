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
}
