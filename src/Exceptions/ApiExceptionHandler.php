<?php

namespace MasudZaman\LaravelApiResponse\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use MasudZaman\LaravelApiResponse\Support\IsApiRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use MasudZaman\LaravelApiResponse\Http\Resources\BaseResponse;
use Throwable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use MasudZaman\LaravelApiResponse\Support\HttpResponse;
use MasudZaman\LaravelApiResponse\Response\ApiResponse;
use BadMethodCallException;

class ApiExceptionHandler extends ExceptionHandler
{
    use IsApiRequest;
    /**
     * Create a new exception handler instance.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     * @return void
     */
    public function __construct($container)
    {
        parent::__construct($container);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Ensure this is an API request
        // Check if middleware set Accept: application/json or if request wants JSON
        if ($this->isApiRequest($request)) {

            // Handle different types of exceptions and return appropriate error responses
            $result = match (true) {
                // Authentication & Authorization Exceptions
                $exception instanceof \Illuminate\Auth\AuthenticationException =>
                $this->unauthorizedException($exception),

                $exception instanceof \Illuminate\Auth\Access\AuthorizationException,
                $exception instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException =>
                $this->forbiddenException($exception),

                // Validation & Form Exceptions
                $exception instanceof \Illuminate\Validation\ValidationException =>
                $this->validationErrorException($exception),

                $exception instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException =>
                $this->tooManyRequestsException($exception),

                // Database & Model Exceptions
                $exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException =>
                $this->modelNotFoundException($exception),

                $exception instanceof \Illuminate\Database\QueryException =>
                $this->databaseErrorException($exception),

                // Handle BadMethodCallException
                $exception instanceof BadMethodCallException =>
                $this->badMethodCallException($exception),

                // HTTP Exceptions
                $exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException =>
                $this->notFoundHttpException($exception),

                $exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException =>
                $this->methodNotAllowedHttpException($exception),

                // File & Upload Exceptions
                $exception instanceof \Illuminate\Http\Exceptions\PostTooLargeException =>
                $this->fileTooLargeException($exception),

                $exception instanceof \Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException =>
                $this->fileNotFoundException($exception),

                // Service & Maintenance Exceptions
                $exception instanceof \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException =>
                $this->serviceUnavailableException($exception),

                // Application-level exceptions with getStatusCode() (e.g., api-gateway BaseException)
                // This catches any exception that provides its own HTTP status code
                method_exists($exception, 'getStatusCode') && method_exists($exception, 'getErrors') =>
                $this->applicationException($exception),

                // Generic HTTP Exceptions
                $exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException =>
                $this->httpException($exception),

                // Default case for unhandled exceptions
                default =>
                $this->defaultException($exception),
            };

            return $result;
        }

        // If not an API request, let the parent handle the error (use default rendering for web)
        return parent::render($request, $exception);
    }

    // Handle Unauthorized Exception (401)
    private function unauthorizedException($exception)
    {
        return $this->buildResponse(Response::HTTP_UNAUTHORIZED, $exception);
    }

    // Handle Forbidden Exception (403)
    private function forbiddenException($exception)
    {
        return $this->buildResponse(Response::HTTP_FORBIDDEN, $exception);
    }

    // Handle Validation Error Exception (422)
    private function validationErrorException($exception)
    {
        $errors = $exception->errors() ?? [];
        return $this->buildResponse(Response::HTTP_UNPROCESSABLE_ENTITY, $exception, null, $errors);
    }

    // Handle Too Many Requests Exception (429)
    private function tooManyRequestsException($exception)
    {
        return $this->buildResponse(Response::HTTP_TOO_MANY_REQUESTS, $exception);
    }

    // Handle Model Not Found Exception (404)
    private function modelNotFoundException($exception)
    {
        return $this->buildResponse(Response::HTTP_NOT_FOUND, $exception);
    }

    // Handle Database Query Error Exception
    // Returns appropriate status code based on error type:
    // - 409 for duplicates/conflicts
    // - 400 for data validation issues
    // - 500 for server/connection errors
    private function databaseErrorException($exception)
    {
        $statusCode = $this->getDatabaseErrorStatusCode($exception);
        $message = $this->getDatabaseErrorMessage($exception);

        return $this->buildResponse(
            $statusCode,
            $exception,
            $message,
            ['database' => $message]
        );
    }

    // Handle Bad Method Call Exception (500)
    private function badMethodCallException($exception)
    {
        return $this->buildResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $exception);
    }

    // Handle HTTP Not Found Exception (404)
    private function notFoundHttpException($exception)
    {
        return $this->buildResponse(Response::HTTP_NOT_FOUND, $exception);
    }

    // Handle Method Not Allowed HTTP Exception (405)
    private function methodNotAllowedHttpException($exception)
    {
        return $this->buildResponse(Response::HTTP_METHOD_NOT_ALLOWED, $exception);
    }

    // Handle File Too Large Exception (413)
    private function fileTooLargeException($exception)
    {
        return $this->buildResponse(Response::HTTP_REQUEST_ENTITY_TOO_LARGE, $exception);
    }

    // Handle File Not Found Exception (404)
    private function fileNotFoundException($exception)
    {
        return $this->buildResponse(Response::HTTP_NOT_FOUND, $exception);
    }

    // Handle Service Unavailable Exception (503)
    private function serviceUnavailableException($exception)
    {
        return $this->buildResponse(Response::HTTP_SERVICE_UNAVAILABLE, $exception);
    }

    // Handle Generic HTTP Exception
    private function httpException($exception)
    {
        return $this->buildResponse(
            $exception->getStatusCode(),
            $exception,
            $exception->getMessage()
        );
    }

    // Handle application-level exceptions (e.g., api-gateway BaseException subclasses)
    // Any exception that implements getStatusCode() and getErrors() methods
    private function applicationException($exception)
    {
        $code = $exception->getStatusCode();
        $errors = $exception->getErrors();

        return exceptionResponse($exception, $code, $exception->getMessage(), $errors);
    }

    // Default case for unhandled exceptions
    private function defaultException($exception)
    {
        return exceptionResponse($exception, Response::HTTP_INTERNAL_SERVER_ERROR, 'An unexpected error occurred');
    }

    /**
     * Builds a standardized response structure for exceptions, and logs the error if necessary.
     * 
     * @param int $code
     * @param Throwable $exception
     * @param string|null $message
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    private function buildResponse(int $code, Throwable $exception, ?string $message = null, ?array $errors = null)
    {
        // Set message if not provided
        $message = $message ?? HttpResponse::getMessage($code);

        // Prepare response data
        $responseData = [
            'status' => HttpResponse::getType($code),
            'message' => ucfirst($message),
            'code' => $code,
        ];

        // Set error_type and error_code if it's an error response
        if (!HttpResponse::isSuccess($code)) {
            $responseData['errors'] = $errors ?? ['error' => $exception->getMessage()];

            $responseData['error_type'] = HttpResponse::getErrorType($exception);
            $responseData['error_code'] = HttpResponse::getErrorCode($exception);
        }

        // Add debug data in non-production environments
        if (!app()->environment('production')) {
            $responseData['data'] = ApiResponse::buildDebugData($exception);
        }

        // Log the exception in non-production environments
        if (!app()->environment('production')) {
            Log::error($message, [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'data' => $responseData['data'] ?? null,
            ]);
        }

        // Return the response with the appropriate HTTP status code
        return response()->json(new BaseResponse($responseData), $code);
    }

    /**
     * Get appropriate error message based on environment.
     * 
     * @param Throwable $e
     * @return string
     */
    protected function getErrorMessage(Throwable $e): string
    {
        return app()->environment('production')
            ? 'An unexpected error occurred.'
            : ($e->getMessage() ?: 'An unexpected error occurred.');
    }

    /**
     * Get appropriate HTTP status code for database exception
     * 
     * @param \Illuminate\Database\QueryException $e
     * @return int
     */
    protected function getDatabaseErrorStatusCode(\Illuminate\Database\QueryException $e): int
    {
        $message = $e->getMessage();

        return match (true) {
            // Conflict errors (409)
            str_contains($message, 'Duplicate entry'),
            str_contains($message, 'UNIQUE constraint failed'),
            str_contains($message, 'unique constraint') => Response::HTTP_CONFLICT,

            // Client errors (400) - data validation issues
            str_contains($message, 'Data too long'),
            str_contains($message, 'Data truncated'),
            str_contains($message, 'Incorrect') && str_contains($message, 'value'),
            str_contains($message, 'Out of range'),
            str_contains($message, 'Invalid') => Response::HTTP_BAD_REQUEST,

            // Foreign key constraint could be 400 or 404 depending on context
            // Using 400 as it's usually a client data issue
            str_contains($message, 'Foreign key constraint'),
            str_contains($message, 'FOREIGN KEY constraint failed') => Response::HTTP_BAD_REQUEST,

            // Server errors (500) - everything else
            default => Response::HTTP_INTERNAL_SERVER_ERROR
        };
    }

    /**
     * Get user-friendly error message for database exception
     * 
     * @param \Illuminate\Database\QueryException $e
     * @return string
     */
    protected function getDatabaseErrorMessage(\Illuminate\Database\QueryException $e): string
    {
        $message = $e->getMessage();

        return match (true) {
            // Duplicate/Unique constraint violations
            str_contains($message, 'Duplicate entry') => 'This record already exists',
            str_contains($message, 'UNIQUE constraint failed') => 'This record already exists',
            str_contains($message, 'unique constraint') => 'This record already exists',

            // Foreign key constraints
            str_contains($message, 'Foreign key constraint') => 'Related record not found or cannot be deleted',
            str_contains($message, 'FOREIGN KEY constraint failed') => 'Related record not found or cannot be deleted',

            // Data validation issues
            str_contains($message, 'Data too long') => 'Data exceeds maximum allowed length',
            str_contains($message, 'Data truncated') => 'Invalid data format provided',
            str_contains($message, 'Out of range') => 'Value is out of acceptable range',

            // Schema issues
            str_contains($message, 'Column not found') => 'Invalid field specified',
            str_contains($message, 'Unknown column') => 'Invalid field specified',
            str_contains($message, 'Table') && str_contains($message, 'doesn\'t exist') => 'Database table not found',

            // Connection issues
            str_contains($message, 'Connection refused') => 'Database connection failed',
            str_contains($message, 'Connection timed out') => 'Database connection timeout',
            str_contains($message, 'Access denied') => 'Database access denied',

            // Default fallback
            default => 'A database error occurred'
        };
    }

}