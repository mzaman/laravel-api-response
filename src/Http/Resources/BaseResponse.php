<?php

namespace MasudZaman\LaravelApiResponse\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use MasudZaman\LaravelApiResponse\Constants\ApiCode;
use MasudZaman\LaravelApiResponse\Constants\ApiMessage;

/**
 * Base Response Class for structured API responses
 */
abstract class BaseResponse extends JsonResource
{
    protected $status;   // Response status ('success', 'error')
    protected $code;     // HTTP Status Code (e.g., 200)
    protected $message;  // Response message
    protected $data;     // Response data (if applicable)
    protected $errors;   // Errors (if applicable)
    protected $meta;     // Meta information (e.g., pagination)
    protected $locale;   // Locale for internationalization

    public function __construct($resource)
    {
        parent::__construct($resource);

        // Initialize values, fall back to defaults if not set
        $this->status = $resource['status'] ?? 'success';
        $this->code = $resource['code'] ?? ApiCode::OK;
        $this->message = $resource['message'] ?? trans('messages.' . $this->getMessageKey($this->code));
        $this->data = $resource['data'] ?? null;
        $this->errors = $resource['errors'] ?? null;
        $this->meta = $resource['meta'] ?? null;
        $this->locale = $resource['locale'] ?? null;
    }

    public function toArray($request)
    {
        $response = [
            'status' => $this->status,
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data,
            'errors' => $this->errors,
        ];

        if ($this->meta) {
            $response['meta'] = $this->meta; // Add pagination or additional meta data
        }

        if ($this->locale) {
            $response['locale'] = $this->locale; // Localization support
        }

        return $response;
    }

    private function getMessageKey($code)
    {
        switch ($code) {
            case ApiCode::CREATED:
                return 'created_success';
            case ApiCode::BAD_REQUEST:
                return 'bad_request';
            case ApiCode::UNAUTHORIZED:
                return 'unauthorized';
            case ApiCode::FORBIDDEN:
                return 'forbidden';
            case ApiCode::NOT_FOUND:
                return 'not_found';
            case ApiCode::INTERNAL_SERVER_ERROR:
                return 'internal_server_error';
            case ApiCode::SERVICE_UNAVAILABLE:
                return 'service_unavailable';
            case ApiCode::CONFLICT:
                return 'conflict';
            case ApiCode::TOO_MANY_REQUESTS:
                return 'rate_limit_exceeded';
            default:
                return 'success';
        }
    }
}
