<?php

namespace mzaman\LaravelApiResponse\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use mzaman\LaravelApiResponse\Constants\HttpStatusCode;

/**
 * Base Response Class
 *
 * This is the standard structure for all API responses, including support for success, locale, error handling, and metadata.
 */
abstract class BaseResponse extends JsonResource
{
    protected $status;       // API-specific status (e.g., API_SUCCESS)
    protected $httpStatus;   // HTTP Status (e.g., 200)
    protected $success;      // Success flag
    protected $message;      // Custom message
    protected $data;         // Data payload
    protected $errors;       // Errors array
    protected $meta;         // Meta information (e.g., pagination)
    protected $locale;       // Locale information (for i18n support)

    /**
     * BaseResponse constructor.
     * @param array $resource
     */
    public function __construct($resource)
    {
        parent::__construct($resource);

        // Assign properties, with defaults
        $this->status = $resource['status'] ?? HttpStatusCode::API_SUCCESS;  // Default to API_SUCCESS
        $this->httpStatus = $resource['httpStatus'] ?? HttpStatusCode::OK;    // Default to HTTP OK
        $this->success = $resource['success'] ?? true;
        $this->message = $resource['message'] ?? HttpStatusCode::SUCCESS_MESSAGE;
        $this->data = $resource['data'] ?? null;
        $this->errors = $resource['errors'] ?? null;
        $this->meta = $resource['meta'] ?? null;
        $this->locale = $resource['locale'] ?? null;
    }

    /**
     * Format the response to an array.
     * This includes status, message, data, errors, etc.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        // Basic response structure
        $response = [
            'status' => $this->status,
            'httpStatus' => $this->httpStatus,
            'success' => $this->success,
            'message' => $this->message,
            'errors' => $this->errors,
            'data' => $this->data,
        ];

        // Include meta information if available (pagination, etc.)
        if ($this->meta) {
            $response['meta'] = $this->meta;
        }

        // Include locale information if available
        if ($this->locale) {
            $response['locale'] = $this->locale;
        }

        return $response;
    }
}
