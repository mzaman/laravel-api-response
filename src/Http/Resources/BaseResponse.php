<?php

namespace MasudZaman\LaravelApiResponse\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use MasudZaman\LaravelApiResponse\Support\HttpResponse;
use Illuminate\Http\Response;

/**
 * Base Response Class for structured API responses with localization support
 */
class BaseResponse extends JsonResource
{
    protected $success;   // Success flag (true for success, false for error)
    protected $status;    // Response status ('success', 'error')
    protected $code;      // HTTP Status Code (e.g., 200)
    protected $message;   // Response message
    protected $data;      // Response data (if applicable)
    protected $errors;    // Errors (if applicable)
    protected $locale;    // Locale for internationalization

    /**
     * Constructor to initialize the response
     * 
     * @param array $resource Response data
     */
    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->code = $resource['code'] ?? Response::HTTP_OK; // Default to 200
        $this->status = $resource['status'] ?? HttpResponse::getType($this->code);
        $this->success = $this->status === 'success'; // Set success flag based on status
        $this->message = $resource['message'] ?? HttpResponse::getMessage($this->code);
        $this->data = $resource['data'] ?? null;
        $this->errors = $resource['errors'] ?? null;
        $this->locale = $resource['locale'] ?? app()->getLocale();
    }

    /**
     * Convert the response data into an array
     * 
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'success' => $this->success,
            'status' => $this->status,
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data,
            'errors' => $this->errors,
            'locale' => $this->locale,
        ];
    }
}
