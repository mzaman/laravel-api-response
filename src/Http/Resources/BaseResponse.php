<?php

namespace MasudZaman\LaravelApiResponse\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use MasudZaman\LaravelApiResponse\Constants\ApiCode;

/**
 * Base Response Class for structured API responses
 */
class BaseResponse extends JsonResource
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

        $this->status = $resource['status'] ?? 'success';
        $this->code = $resource['code'] ?? ApiCode::OK; // Default to OK (200)
        $this->message = $resource['message'] ?? $this->getMessageByCode($this->code);
        $this->data = $resource['data'] ?? null;
        $this->errors = $resource['errors'] ?? null;
        $this->meta = $resource['meta'] ?? null;
        $this->locale = $resource['locale'] ?? app()->getLocale();
    }

    public function toArray($request)
    {
        return [
            'status' => $this->status,
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data,
            'errors' => $this->errors,
            'meta' => $this->meta,
            'locale' => $this->locale,
        ];
    }

    private function getMessageByCode($code)
    {
        $messages = config('api-response.messages', []);
        
        switch ($code) {
            case ApiCode::OK:
                return $messages['success'];
            case ApiCode::CREATED:
                return $messages['created_success'];
            case ApiCode::BAD_REQUEST:
                return $messages['bad_request'];
            case ApiCode::UNAUTHORIZED:
                return $messages['unauthorized'];
            case ApiCode::FORBIDDEN:
                return $messages['forbidden'];
            case ApiCode::NOT_FOUND:
                return $messages['not_found'];
            case ApiCode::INTERNAL_SERVER_ERROR:
                return $messages['internal_server_error'];
            case ApiCode::SERVICE_UNAVAILABLE:
                return $messages['service_unavailable'];
            case ApiCode::CONFLICT:
                return $messages['conflict'];
            case ApiCode::TOO_MANY_REQUESTS:
                return $messages['rate_limit_exceeded'];
            default:
                return $messages[$code] ?? 'An unexpected error occurred';
        }
    }
}
