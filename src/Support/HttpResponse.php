<?php

namespace MasudZaman\LaravelApiResponse\Support;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;
use Throwable;

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
            Response::HTTP_OK => 'The request was successful and the response is as expected.',
            Response::HTTP_CREATED => 'The resource has been successfully created.',
            Response::HTTP_ACCEPTED => 'Your request has been accepted and is being processed.',
            Response::HTTP_NON_AUTHORITATIVE_INFORMATION => 'The response contains non-authoritative information.',
            Response::HTTP_NO_CONTENT => 'No content to display in the response.',
            Response::HTTP_RESET_CONTENT => 'The content has been reset successfully.',
            Response::HTTP_PARTIAL_CONTENT => 'Partial content is returned due to the request range.',

            // 3xx Redirection
            Response::HTTP_MULTIPLE_CHOICES => 'Multiple options are available for the resource you requested.',
            Response::HTTP_MOVED_PERMANENTLY => 'The resource has been permanently moved to a new location.',
            Response::HTTP_FOUND => 'The resource has been found at a different location and redirected.',
            Response::HTTP_SEE_OTHER => 'Please refer to another resource to complete your request.',
            Response::HTTP_NOT_MODIFIED => 'The resource has not been modified since the last request.',
            Response::HTTP_USE_PROXY => 'A proxy server must be used to access the resource.',
            Response::HTTP_TEMPORARY_REDIRECT => 'The resource has temporarily moved, please try again later.',
            Response::HTTP_PERMANENTLY_REDIRECT => 'The resource has been permanently redirected to a new location.',

            // 4xx Client Error
            Response::HTTP_BAD_REQUEST => 'Your request is malformed or contains invalid parameters. Please check and try again.',
            Response::HTTP_UNAUTHORIZED => 'You need to authenticate to access this resource.',
            Response::HTTP_PAYMENT_REQUIRED => 'Payment is required to proceed with the request.',
            Response::HTTP_FORBIDDEN => 'You do not have permission to access this resource.',
            Response::HTTP_NOT_FOUND => 'The resource you are looking for could not be found.',
            Response::HTTP_METHOD_NOT_ALLOWED => 'The HTTP method used is not allowed for this resource.',
            Response::HTTP_NOT_ACCEPTABLE => 'The request is not acceptable as per the provided headers.',
            Response::HTTP_PROXY_AUTHENTICATION_REQUIRED => 'Authentication through a proxy server is required.',
            Response::HTTP_REQUEST_TIMEOUT => 'The server timed out waiting for the request.',
            Response::HTTP_CONFLICT => 'There is a conflict with the current state of the resource.',
            Response::HTTP_GONE => 'The resource is no longer available and has been permanently removed.',
            Response::HTTP_LENGTH_REQUIRED => 'The request requires a valid Content-Length header.',
            Response::HTTP_PRECONDITION_FAILED => 'The request preconditions were not met.',
            Response::HTTP_REQUEST_ENTITY_TOO_LARGE => 'The request entity is too large for the server to process.',
            Response::HTTP_REQUEST_URI_TOO_LONG => 'The URI of the request is too long. Please shorten it and try again.',
            Response::HTTP_UNSUPPORTED_MEDIA_TYPE => 'The media type of the request is not supported by the server.',
            Response::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE => 'The requested range is not satisfiable. Please check your range request.',
            Response::HTTP_EXPECTATION_FAILED => 'The expectation given in the request header could not be met.',
            Response::HTTP_I_AM_A_TEAPOT => 'I am a teapot. This is an April Fools joke, try again later.',
            Response::HTTP_MISDIRECTED_REQUEST => 'The request was directed to the wrong server or endpoint. Please check the request URL and try again.',
            Response::HTTP_UNPROCESSABLE_ENTITY => 'The request data was invalid or unprocessable.',
            Response::HTTP_LOCKED => 'The resource is currently locked and cannot be modified.',
            Response::HTTP_FAILED_DEPENDENCY => 'The request failed due to a failed dependency.',
            Response::HTTP_TOO_EARLY => 'The request was refused because it was too early.',
            Response::HTTP_UPGRADE_REQUIRED => 'The request requires an upgrade to a newer protocol.',
            Response::HTTP_PRECONDITION_REQUIRED => 'The request requires specific preconditions to be fulfilled.',
            Response::HTTP_TOO_MANY_REQUESTS => 'You have exceeded the allowed number of requests. Please try again later.',
            Response::HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE => 'The request headers are too large to be processed.',
            Response::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS => 'The requested resource is unavailable due to legal reasons.',

            // 5xx Server Error
            Response::HTTP_INTERNAL_SERVER_ERROR => 'The server encountered an unexpected condition that prevented it from fulfilling the request.',
            Response::HTTP_NOT_IMPLEMENTED => 'The server does not support the functionality required to fulfill the request.',
            Response::HTTP_BAD_GATEWAY => 'The server received an invalid response from the upstream server.',
            Response::HTTP_SERVICE_UNAVAILABLE => 'The service is temporarily unavailable. Please try again later.',
            Response::HTTP_GATEWAY_TIMEOUT => 'The server did not receive a timely response from an upstream server.',
            Response::HTTP_VERSION_NOT_SUPPORTED => 'The HTTP version used is not supported by the server.',
            Response::HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL => 'The server cannot fulfill the request due to negotiation issues.',
            Response::HTTP_INSUFFICIENT_STORAGE => 'The server cannot store the representation needed to fulfill the request.',
            Response::HTTP_LOOP_DETECTED => 'A loop was detected while processing your request.',
            Response::HTTP_NOT_EXTENDED => 'The server does not support the required extension.',
            Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED => 'Network authentication is required to complete the request.',

            default => 'An unknown error occurred while processing your request.',
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
    
    /**
     * Get error type based on exception
     *
     * @param \Throwable $exception
     * @return string
     */
    public static function getErrorType(Throwable $exception): string
    {
        return match (true) {
            // Authentication & Authorization Exceptions
            $exception instanceof \Illuminate\Auth\AuthenticationException,
            $exception instanceof \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException,
            $exception instanceof \Illuminate\Auth\Access\AuthorizationException,
            $exception instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException => 'authorization',

            // Validation Exceptions
            $exception instanceof \Illuminate\Validation\ValidationException => 'validation',

            // Client Errors (4xx)
            $exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
            $exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException,
            $exception instanceof \Symfony\Component\HttpKernel\Exception\BadRequestHttpException,
            $exception instanceof \Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException,
            $exception instanceof \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException,
            $exception instanceof \Symfony\Component\HttpKernel\Exception\ConflictHttpException,
            $exception instanceof \Symfony\Component\HttpKernel\Exception\MisdirectedRequestHttpException,
            $exception instanceof \Symfony\Component\HttpKernel\Exception\LengthRequiredHttpException,
            $exception instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException,
            $exception instanceof \Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException,
            $exception instanceof \Symfony\Component\HttpKernel\Exception\ExpectationFailedHttpException => 'client_error',

            // Server Errors (5xx)
            $exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException,
            $exception instanceof \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException,
            $exception instanceof \Illuminate\Database\QueryException,
            $exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException,
            $exception instanceof \Illuminate\Database\Eloquent\MassAssignmentException,
            $exception instanceof \Illuminate\Filesystem\FileNotFoundException,
            $exception instanceof \Illuminate\Queue\QueueException,
            $exception instanceof \Illuminate\Cache\CacheException,
            $exception instanceof \Illuminate\Encryption\DecryptException,
            $exception instanceof \Illuminate\Broadcasting\BroadcastException,
            $exception instanceof \Illuminate\Mail\MailException,
            $exception instanceof \Illuminate\Routing\Exceptions\UrlGenerationException,
            $exception instanceof \Illuminate\Routing\Exceptions\RedirectException,
            $exception instanceof \Illuminate\Session\TokenMismatchException,
            $exception instanceof \Illuminate\Http\Exceptions\PostTooLargeException,
            $exception instanceof \Illuminate\Database\Eloquent\UniqueConstraintViolationException => 'server_error',

            // Default to 'server_error' for unknown exceptions
            default => 'server_error',
        };
    }

    /**
     * Get error code based on exception
     *
     * @param \Throwable $exception
     * @return string
     */
    public static function getErrorCode(Throwable $exception): string
    {
        return match (true) {
            // Authentication & Authorization Exceptions
            $exception instanceof \Illuminate\Auth\AuthenticationException => 'ERR_AUTHENTICATION_FAILED',
            $exception instanceof \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException => 'ERR_UNAUTHORIZED_ACCESS',
            
            $exception instanceof \Illuminate\Auth\Access\AuthorizationException => 'ERR_ACCESS_DENIED',
            $exception instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException => 'ERR_ACCESS_DENIED',

            // Validation Exceptions
            $exception instanceof \Illuminate\Validation\ValidationException => 'ERR_VALIDATION_FAILED',

            // Client Errors (4xx)
            $exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException => 'ERR_RESOURCE_NOT_FOUND',
            $exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException => 'ERR_METHOD_NOT_ALLOWED',
            $exception instanceof \Symfony\Component\HttpKernel\Exception\BadRequestHttpException => 'ERR_BAD_REQUEST',
            $exception instanceof \Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException => 'ERR_UNPROCESSABLE_ENTITY',
            $exception instanceof \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException => 'ERR_TOO_MANY_REQUESTS',
            $exception instanceof \Symfony\Component\HttpKernel\Exception\ConflictHttpException => 'ERR_CONFLICT',
            $exception instanceof \Symfony\Component\HttpKernel\Exception\MisdirectedRequestHttpException => 'ERR_MISDIRECTED_REQUEST',
            $exception instanceof \Symfony\Component\HttpKernel\Exception\LengthRequiredHttpException => 'ERR_LENGTH_REQUIRED',
            $exception instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException => 'ERR_RATE_LIMIT_EXCEEDED',
            $exception instanceof \Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException => 'ERR_UNSUPPORTED_MEDIA_TYPE',
            $exception instanceof \Symfony\Component\HttpKernel\Exception\ExpectationFailedHttpException => 'ERR_EXPECTATION_FAILED',

            // Server Errors (5xx)
            $exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException => 'ERR_UNKNOWN_HTTP_EXCEPTION',
            $exception instanceof \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException => 'ERR_SERVICE_UNAVAILABLE',
            $exception instanceof \Illuminate\Database\QueryException => 'ERR_DATABASE_QUERY_EXCEPTION',
            $exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException => 'ERR_MODEL_NOT_FOUND',
            $exception instanceof \Illuminate\Database\Eloquent\MassAssignmentException => 'ERR_MASS_ASSIGNMENT_ERROR',
            $exception instanceof \Illuminate\Filesystem\FileNotFoundException => 'ERR_FILE_NOT_FOUND',
            $exception instanceof \Illuminate\Queue\QueueException => 'ERR_QUEUE_EXCEPTION',
            $exception instanceof \Illuminate\Cache\CacheException => 'ERR_CACHE_EXCEPTION',
            $exception instanceof \Illuminate\Encryption\DecryptException => 'ERR_DECRYPTION_FAILED',
            $exception instanceof \Illuminate\Broadcasting\BroadcastException => 'ERR_BROADCASTING_EXCEPTION',
            $exception instanceof \Illuminate\Mail\MailException => 'ERR_MAIL_EXCEPTION',
            $exception instanceof \Illuminate\Routing\Exceptions\UrlGenerationException => 'ERR_URL_GENERATION_EXCEPTION',
            $exception instanceof \Illuminate\Routing\Exceptions\RedirectException => 'ERR_REDIRECT_EXCEPTION',

            // Custom server errors
            $exception instanceof \Illuminate\Session\TokenMismatchException => 'ERR_SESSION_TOKEN_MISMATCH',
            $exception instanceof \Illuminate\Http\Exceptions\PostTooLargeException => 'ERR_REQUEST_TOO_LARGE',
            
            // Database-specific errors
            $exception instanceof \Illuminate\Database\Eloquent\UniqueConstraintViolationException => 'ERR_UNIQUE_CONSTRAINT_VIOLATION',

            // Default to 'ERR_UNKNOWN_ERROR' for unknown exceptions
            default => 'ERR_UNKNOWN_ERROR',
        };
    }

}