<?php

namespace MasudZaman\LaravelApiResponse\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ApiResponseException extends Exception
{
    protected $statusCode;

    public function __construct($message = 'Error occurred', $code = 500)
    {
        parent::__construct($message, $code);
        $this->statusCode = $code;
    }

    /**
     * Customize the response to be returned.
     *
     * @return JsonResponse
     */
    public function render()
    {
        return response()->json([
            'success' => false,
            'status' => 'error',
            'code' => $this->statusCode,
            'message' => $this->getMessage(),
        ], $this->statusCode);
    }
}
