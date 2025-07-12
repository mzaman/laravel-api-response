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
    protected $success;     // Success flag (true for success, false for error)
    protected $status;      // Response status ('success', 'error')
    protected $code;        // HTTP Status Code (e.g., 200)
    protected $message;     // Response message
    protected $error_type;  // Error type/category (e.g., "validation", "authorization")
    protected $error_code;  // Unique code for the error (e.g., "VALIDATION_FAILED")
    protected $data;        // Response data (if applicable)
    protected $errors;      // Errors (if applicable)
    protected $locale;      // Locale for internationalization

    /**
     * Constructor to initialize the response
     * 
     * @param array $resource Response data
     */
    public function __construct($resource)
    {
        parent::__construct($resource);

        // Default to HTTP_OK (200)
        $this->code = $resource['code'] ?? Response::HTTP_OK;

        // Set the response status based on code (success or error)
        $this->status = $resource['status'] ?? HttpResponse::getType($this->code);
        $this->success = $this->status === 'success'; // If status is 'success', set the success flag to true

        // Set message, fallback to HttpResponse if not provided
        $this->message = $resource['message'] ?? HttpResponse::getMessage($this->code);

        // Set error_type and error_code if it's an error response
        if (in_array($this->status, ['fail', 'error'])) {
            $this->error_type = $resource['error_type'] ?? 'server_error'; // Default to 'server_error'
            $this->error_code = $resource['error_code'] ?? 'UNKNOWN_ERROR'; // Default to 'UNKNOWN_ERROR'
        }

        // Assign other data values
        $this->data = $resource['data'] ?? null;
        $this->errors = $resource['errors'] ?? null;

        // Set the locale, default to the app locale
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
            'error_type' => $this->error_type,
            'error_code' => $this->error_code,
            'data' => $this->data,
            'errors' => $this->errors,
            'locale' => $this->locale,
        ];
    }
}
