<?php

namespace mzaman\LaravelApiResponse\Exceptions;

use Exception;

abstract class ApiException extends Exception
{
    protected $status;
    protected $httpStatus;
    protected $message;
    protected $errors;

    public function __construct($message, $status = 1000, $httpStatus = 400, $errors = null)
    {
        $this->message = $message;
        $this->status = $status;
        $this->httpStatus = $httpStatus;
        $this->errors = $errors;
        parent::__construct($message);
    }

    public function render($request)
    {
        return response()->json(new \mzaman\LaravelApiResponse\Http\Resources\BaseResponse([
            'status' => $this->status,
            'httpStatus' => $this->httpStatus,
            'success' => false,
            'message' => $this->message,
            'errors' => $this->errors,
        ]), $this->httpStatus);
    }
}
