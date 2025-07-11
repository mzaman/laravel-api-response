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
        $this->message = $resource['message'] ?? $this->getMessageByCode($this->code);
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

    /**
     * Get the message by code considering localization and fallback logic.
     * 
     * @param int $code
     * @return string
     */
    private function getMessageByCode(int $code): string
    {
        // Convert HTTP status code to snake_case for the key
        $messageKey = $this->getMessageKeyForCode($code);

        // Retrieve the custom message from the config file based on the snake_case key
        $customMessages = config('api-response.messages', []);
        $message = $customMessages[$messageKey] ?? HttpResponse::getMessage($code);

        // Return the localized message, or the default message if the locale is not found
        return __($message);
    }

    /**
     * Convert HTTP status code to snake_case string key.
     *
     * @param int $code
     * @return string
     */
    private function getMessageKeyForCode(int $code): string
    {
        return match ($code) {
            // 2xx Success
            Response::HTTP_OK => 'success',
            Response::HTTP_CREATED => 'created_success',
            Response::HTTP_ACCEPTED => 'accepted',
            Response::HTTP_NON_AUTHORITATIVE_INFORMATION => 'non_authoritative_information',
            Response::HTTP_NO_CONTENT => 'no_content',
            Response::HTTP_RESET_CONTENT => 'reset_content',
            Response::HTTP_PARTIAL_CONTENT => 'partial_content',

            // 3xx Redirection
            Response::HTTP_MULTIPLE_CHOICES => 'multiple_choices',
            Response::HTTP_MOVED_PERMANENTLY => 'moved_permanently',
            Response::HTTP_FOUND => 'found',
            Response::HTTP_SEE_OTHER => 'see_other',
            Response::HTTP_NOT_MODIFIED => 'not_modified',
            Response::HTTP_USE_PROXY => 'use_proxy',
            Response::HTTP_TEMPORARY_REDIRECT => 'temporary_redirect',
            Response::HTTP_PERMANENTLY_REDIRECT => 'permanent_redirect',

            // 4xx Client Error
            Response::HTTP_BAD_REQUEST => 'bad_request',
            Response::HTTP_UNAUTHORIZED => 'unauthorized',
            Response::HTTP_PAYMENT_REQUIRED => 'payment_required',
            Response::HTTP_FORBIDDEN => 'forbidden',
            Response::HTTP_NOT_FOUND => 'not_found',
            Response::HTTP_METHOD_NOT_ALLOWED => 'method_not_allowed',
            Response::HTTP_NOT_ACCEPTABLE => 'not_acceptable',
            Response::HTTP_PROXY_AUTHENTICATION_REQUIRED => 'proxy_authentication_required',
            Response::HTTP_REQUEST_TIMEOUT => 'request_timeout',
            Response::HTTP_CONFLICT => 'conflict',
            Response::HTTP_GONE => 'gone',
            Response::HTTP_LENGTH_REQUIRED => 'length_required',
            Response::HTTP_PRECONDITION_FAILED => 'precondition_failed',
            Response::HTTP_REQUEST_ENTITY_TOO_LARGE => 'request_entity_too_large',
            Response::HTTP_REQUEST_URI_TOO_LONG => 'request_uri_too_long',
            Response::HTTP_UNSUPPORTED_MEDIA_TYPE => 'unsupported_media_type',
            Response::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE => 'requested_range_not_satisfiable',
            Response::HTTP_EXPECTATION_FAILED => 'expectation_failed',
            Response::HTTP_I_AM_A_TEAPOT => 'i_am_a_teapot',
            Response::HTTP_UNPROCESSABLE_ENTITY => 'unprocessable_entity',
            Response::HTTP_LOCKED => 'locked',
            Response::HTTP_FAILED_DEPENDENCY => 'failed_dependency',
            Response::HTTP_TOO_EARLY => 'too_early',
            Response::HTTP_UPGRADE_REQUIRED => 'upgrade_required',
            Response::HTTP_PRECONDITION_REQUIRED => 'precondition_required',
            Response::HTTP_TOO_MANY_REQUESTS => 'rate_limit_exceeded',
            Response::HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE => 'request_header_fields_too_large',
            Response::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS => 'unavailable_for_legal_reasons',

            // 5xx Server Error
            Response::HTTP_INTERNAL_SERVER_ERROR => 'internal_server_error',
            Response::HTTP_NOT_IMPLEMENTED => 'not_implemented',
            Response::HTTP_BAD_GATEWAY => 'bad_gateway',
            Response::HTTP_SERVICE_UNAVAILABLE => 'service_unavailable',
            Response::HTTP_GATEWAY_TIMEOUT => 'gateway_timeout',
            Response::HTTP_VERSION_NOT_SUPPORTED => 'http_version_not_supported',
            Response::HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL => 'variant_also_negotiates',
            Response::HTTP_INSUFFICIENT_STORAGE => 'insufficient_storage',
            Response::HTTP_LOOP_DETECTED => 'loop_detected',
            Response::HTTP_NOT_EXTENDED => 'not_extended',
            Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED => 'network_authentication_required',

            default => 'unknown_status_code'
        };
    }

}
