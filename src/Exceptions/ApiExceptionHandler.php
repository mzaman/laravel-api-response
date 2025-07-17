<?php

namespace MasudZaman\LaravelApiResponse\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use MasudZaman\LaravelApiResponse\Http\Resources\BaseResponse;
use Throwable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use MasudZaman\LaravelApiResponse\Support\HttpResponse;

class ApiExceptionHandler extends ExceptionHandler
{
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
        if ($request->is('api/*') || $request->wantsJson()) {

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

    // Handle Database Query Error Exception (500)
    private function databaseErrorException($exception)
    {
        return $this->buildResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $exception, 'Database Error', ['database' => $this->getDatabaseErrorMessage($exception)], 'server_error', 'DATABASE_ERROR');
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

    // Handle Generic HTTP Exception (500)
    private function httpException($exception)
    {
        return $this->buildResponse($exception->getStatusCode(), $exception, $exception->getMessage(), null, 'server_error', 'UNKNOWN_ERROR');
    }

    // Default case for unhandled exceptions
    private function defaultException($exception)
    {
        return $this->buildResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $exception, 'An unexpected error occurred');
    }

    /**
     * Builds a standardized response structure for exceptions, and logs the error if necessary.
     * 
     * @param int $code
     * @param Throwable $exception
     * @param string|null $message
     * @param array|null $errors
     * @return \Illuminate\Http\Response
     */
    private function buildResponse(int $code, Throwable $exception, string $message = null, array $errors = null)
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

        // Add data to response if needed
        if (!app()->environment('production')) {
            $responseData['data'] = [
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        }

        // Log the exception if the environment is not production
        if (!app()->environment('production')) {
            Log::error($message, [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'data' => $responseData['data'],
            ]);
        }

        // Return the response with the appropriate HTTP status code
        return (new BaseResponse($responseData))->response()->setStatusCode($code);
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
     * Get database-specific error message
     * 
     * @param \Illuminate\Database\QueryException $e
     * @return string
     */
    protected function getDatabaseErrorMessage(\Illuminate\Database\QueryException $e): string
    {
        return match (true) {
            str_contains($e->getMessage(), 'Duplicate entry') => 'Duplicate entry found.',
            str_contains($e->getMessage(), 'Foreign key constraint') => 'Related record not found.',
            str_contains($e->getMessage(), 'Data too long') => 'Data exceeds maximum length.',
            str_contains($e->getMessage(), 'Column not found') => 'Invalid database column.',
            str_contains($e->getMessage(), 'Table') && str_contains($e->getMessage(), 'doesn\'t exist') => 'Database table not found.',
            str_contains($e->getMessage(), 'Connection refused') => 'Database connection failed.',
            default => $this->getErrorMessage($e)
        };
    }
}
