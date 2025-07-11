<?php

namespace MasudZaman\LaravelApiResponse\Support;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;

class HttpResponse
{
    /**
     * Get response type based on status code
     * 
     * @param int $code
     * @return string
     */
    public static function getType(int $code): string
    {
        return match (true) {
            $code >= 200 && $code < 300 => 'success',  // 2xx Success
            $code >= 300 && $code < 400 => 'fail',     // 3xx Redirection
            $code >= 400 && $code < 500 => 'fail',     // 4xx Client Error
            $code >= 500 && $code < 600 => 'error',    // 5xx Server Error
            default => 'error'
        };
    }

    /**
     * Get default message for HTTP status code
     * 
     * @param int $code
     * @return string
     */
    public static function getDefaultMessage(int $code): string
    {
        return match ($code) {
            // 2xx Success
            Response::HTTP_OK => 'Success',
            Response::HTTP_CREATED => 'Resource created successfully',
            Response::HTTP_ACCEPTED => 'Request accepted',
            Response::HTTP_NON_AUTHORITATIVE_INFORMATION => 'Non-authoritative information',
            Response::HTTP_NO_CONTENT => 'No content',
            Response::HTTP_RESET_CONTENT => 'Reset content',
            Response::HTTP_PARTIAL_CONTENT => 'Partial content',

            // 3xx Redirection
            Response::HTTP_MULTIPLE_CHOICES => 'Multiple choices',
            Response::HTTP_MOVED_PERMANENTLY => 'Moved permanently',
            Response::HTTP_FOUND => 'Found',
            Response::HTTP_SEE_OTHER => 'See other',
            Response::HTTP_NOT_MODIFIED => 'Not modified',
            Response::HTTP_USE_PROXY => 'Use proxy',
            Response::HTTP_TEMPORARY_REDIRECT => 'Temporary redirect',
            Response::HTTP_PERMANENTLY_REDIRECT => 'Permanent redirect',

            // 4xx Client Error
            Response::HTTP_BAD_REQUEST => 'Bad request',
            Response::HTTP_UNAUTHORIZED => 'Unauthorized',
            Response::HTTP_PAYMENT_REQUIRED => 'Payment required',
            Response::HTTP_FORBIDDEN => 'Forbidden',
            Response::HTTP_NOT_FOUND => 'Not found',
            Response::HTTP_METHOD_NOT_ALLOWED => 'Method not allowed',
            Response::HTTP_NOT_ACCEPTABLE => 'Not acceptable',
            Response::HTTP_PROXY_AUTHENTICATION_REQUIRED => 'Proxy authentication required',
            Response::HTTP_REQUEST_TIMEOUT => 'Request timeout',
            Response::HTTP_CONFLICT => 'Conflict',
            Response::HTTP_GONE => 'Gone',
            Response::HTTP_LENGTH_REQUIRED => 'Length required',
            Response::HTTP_PRECONDITION_FAILED => 'Precondition failed',
            Response::HTTP_REQUEST_ENTITY_TOO_LARGE => 'Request entity too large',
            Response::HTTP_REQUEST_URI_TOO_LONG => 'Request URI too long',
            Response::HTTP_UNSUPPORTED_MEDIA_TYPE => 'Unsupported media type',
            Response::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE => 'Requested range not satisfiable',
            Response::HTTP_EXPECTATION_FAILED => 'Expectation failed',
            Response::HTTP_I_AM_A_TEAPOT => 'I am a teapot',
            Response::HTTP_MISDIRECTED_REQUEST => 'Misdirected request',
            Response::HTTP_UNPROCESSABLE_ENTITY => 'Validation failed',
            Response::HTTP_LOCKED => 'Locked',
            Response::HTTP_FAILED_DEPENDENCY => 'Failed dependency',
            Response::HTTP_TOO_EARLY => 'Too early',
            Response::HTTP_UPGRADE_REQUIRED => 'Upgrade required',
            Response::HTTP_PRECONDITION_REQUIRED => 'Precondition required',
            Response::HTTP_TOO_MANY_REQUESTS => 'Too many requests',
            Response::HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE => 'Request header fields too large',
            Response::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS => 'Unavailable for legal reasons',

            // 5xx Server Error
            Response::HTTP_INTERNAL_SERVER_ERROR => 'Internal server error',
            Response::HTTP_NOT_IMPLEMENTED => 'Not implemented',
            Response::HTTP_BAD_GATEWAY => 'Bad gateway',
            Response::HTTP_SERVICE_UNAVAILABLE => 'Service unavailable 121',
            Response::HTTP_GATEWAY_TIMEOUT => 'Gateway timeout',
            Response::HTTP_VERSION_NOT_SUPPORTED => 'HTTP version not supported',
            Response::HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL => 'Variant also negotiates',
            Response::HTTP_INSUFFICIENT_STORAGE => 'Insufficient storage',
            Response::HTTP_LOOP_DETECTED => 'Loop detected',
            Response::HTTP_NOT_EXTENDED => 'Not extended',
            Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED => 'Network authentication required',

            default => 'Unknown status code'
        };
    }

    /**
     * Get the message by code considering localization and fallback logic.
     * 
     * @param int $code
     * @return string
     */
    public static function getMessage(int $code): string
    {
        // Convert HTTP status code to snake_case for the key
        $messageKey = self::getMessageKeyForCode($code);

        // Check if the translation exists for the message key
        // If it exists, use it; if not, fallback to the default message
        return Lang::has('api.' . $messageKey) ? __('api.' . $messageKey) : self::getDefaultMessage($code);
    }

    /**
     * Convert HTTP status code to snake_case string key.
     *
     * @param int $code
     * @return string
     */
    private static function getMessageKeyForCode(int $code): string
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

    /**
     * Get default code for response type
     * 
     * @param string $type
     * @return int
     */
    public static function getDefaultCodeForType(string $type): int
    {
        return match ($type) {
            'success' => Response::HTTP_OK,
            'fail' => Response::HTTP_BAD_REQUEST,
            'error' => Response::HTTP_INTERNAL_SERVER_ERROR,
            default => Response::HTTP_INTERNAL_SERVER_ERROR
        };
    }

    /**
     * Check if status code is success
     * 
     * @param int $code
     * @return bool
     */
    public static function isSuccess(int $code): bool
    {
        return $code >= 200 && $code < 300;
    }

    /**
     * Check if status code is redirect
     * 
     * @param int $code
     * @return bool
     */
    public static function isRedirect(int $code): bool
    {
        return $code >= 300 && $code < 400;
    }

    /**
     * Check if status code is client error
     * 
     * @param int $code
     * @return bool
     */
    public static function isClientError(int $code): bool
    {
        return $code >= 400 && $code < 500;
    }

    /**
     * Check if status code is server error
     * 
     * @param int $code
     * @return bool
     */
    public static function isServerError(int $code): bool
    {
        return $code >= 500 && $code < 600;
    }
}