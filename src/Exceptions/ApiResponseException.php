<?php

namespace MasudZaman\LaravelApiResponse\Exceptions;

use Exception;

/**
 * ApiResponseException handles custom API errors
 */
class ApiResponseException extends Exception
{
    protected $status;
    protected $code;

    public function __construct($message, $status, $code)
    {
        $this->status = $status;
        $this->code = $code;
        parent::__construct($message);
    }

    public function render($request)
    {
        return response()->json([
            'status' => $this->status,
            'code' => $this->code,
            'message' => $this->getMessage(),
        ], $this->code);
    }
}
