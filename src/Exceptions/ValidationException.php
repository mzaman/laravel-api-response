<?php

namespace MasudZaman\LaravelApiResponse\Exceptions;

use Illuminate\Validation\ValidationException as LaravelValidationException;
use MasudZaman\LaravelApiResponse\Http\Resources\BaseResponse;

/**
 * Custom ValidationException to format errors using the package's response structure
 */
class ValidationException extends LaravelValidationException
{
    /**
     * The structured response data
     *
     * @var array
     */
    protected $responseData;

    /**
     * Create a new ValidationException instance.
     *
     * @param  array  $responseData
     * @return void
     */
    public function __construct(array $responseData)
    {
        $this->responseData = $responseData;

        // Set the message to the first error message in the response
        $this->message = $responseData['message'];
    }

    /**
     * Convert the validation exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        // Use BaseResponse from your package to format the response
        return (new BaseResponse($this->responseData))
            ->response()
            ->setStatusCode($this->responseData['code']);
    }
}
