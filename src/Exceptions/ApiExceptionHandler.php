<?php

namespace MasudZaman\LaravelApiResponse\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use MasudZaman\LaravelApiResponse\Http\Resources\BaseResponse;
use Throwable;
use Illuminate\Http\Response;

class ApiExceptionHandler extends ExceptionHandler
{
    /**
     * Create a new exception handler instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return void
     */
    public function __construct($container)
    {
        // Call parent constructor with the container
        parent::__construct($container);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Initialize the default error response structure
        $errorResponse = [
            'success' => false,  // Indicating the request was not successful
            'status' => 'error',  // Generic error status
            'code' => Response::HTTP_INTERNAL_SERVER_ERROR,  // Default error code for unknown exceptions
            'message' => $this->getErrorMessage($exception),  // General error message
            'data' => null,  // No additional data for errors
            'errors' => [  // Specific error details
                'exception' => [get_class($exception)],  // The exception class
                'message' => $exception->getMessage(),  // Exception message
            ],
            'meta' => null,  // Meta data can be added here, if needed
            'locale' => app()->getLocale(),  // The current app locale
        ];

        // If the environment is not production, include detailed error information
        if (!app()->environment('production')) {
            $errorResponse['data'] = [
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTrace(),
            ];
        }

        // Exception handling logic for specific exception types
        return match (true) {
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
    }

    // Handle Unauthorized Exception
    private function unauthorizedException($exception)
    {
        return (new BaseResponse([
            'status' => 'error',
            'message' => 'Unauthenticated',
            'errors' => ['authentication' => $exception->getMessage()],
            'code' => Response::HTTP_UNAUTHORIZED,
        ]))->response()->setStatusCode(Response::HTTP_UNAUTHORIZED);
    }

    // Handle Forbidden Exception
    private function forbiddenException($exception)
    {
        return (new BaseResponse([
            'status' => 'error',
            'message' => 'Unauthorized action',
            'errors' => ['authorization' => $exception->getMessage()],
            'code' => Response::HTTP_FORBIDDEN,
        ]))->response()->setStatusCode(Response::HTTP_FORBIDDEN);
    }

    // Handle Validation Error Exception
    private function validationErrorException($exception)
    {
        // Check if the exception is an instance of ValidationException
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            // If there are validation errors, ensure we access them safely
            $errors = $exception->errors() ?? [];  // Ensure errors() is called only if it exists

            return (new BaseResponse([
                'status' => 'error',
                'message' => 'The given data was invalid',
                'errors' => $errors,  // Include errors if available, else an empty array
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ]))->response()->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            // If it's not a ValidationException, fallback with a generic error message
            return (new BaseResponse([
                'status' => 'error',
                'message' => 'Unexpected error occurred during validation',
                'errors' => ['validation' => 'An unknown validation error occurred.'],
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]))->response()->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    // Handle Too Many Requests Exception
    private function tooManyRequestsException($exception)
    {
        return (new BaseResponse([
            'status' => 'error',
            'message' => 'Too Many Attempts',
            'errors' => ['throttle' => $exception->getMessage()],
            'code' => Response::HTTP_TOO_MANY_REQUESTS,
        ]))->response()->setStatusCode(Response::HTTP_TOO_MANY_REQUESTS);
    }

    // Handle Model Not Found Exception
    private function modelNotFoundException($exception)
    {
        return (new BaseResponse([
            'status' => 'error',
            'message' => 'Resource not found',
            'errors' => ['model' => 'The requested resource was not found.'],
            'code' => Response::HTTP_NOT_FOUND,
        ]))->response()->setStatusCode(Response::HTTP_NOT_FOUND);
    }

    // Handle Database Query Error Exception
    private function databaseErrorException($exception)
    {
        return (new BaseResponse([
            'status' => 'error',
            'message' => 'Database Error',
            'errors' => ['database' => $this->getDatabaseErrorMessage($exception)],
            'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
        ]))->response()->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    // Handle HTTP Not Found Exception
    private function notFoundHttpException($exception)
    {
        return (new BaseResponse([
            'status' => 'error',
            'message' => 'Not Found',
            'errors' => ['http' => $exception->getMessage()],
            'code' => Response::HTTP_NOT_FOUND,
        ]))->response()->setStatusCode(Response::HTTP_NOT_FOUND);
    }

    // Handle Method Not Allowed HTTP Exception
    private function methodNotAllowedHttpException($exception)
    {
        return (new BaseResponse([
            'status' => 'error',
            'message' => 'Method Not Allowed',
            'errors' => ['method' => $exception->getMessage()],
            'code' => Response::HTTP_METHOD_NOT_ALLOWED,
        ]))->response()->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    // Handle File Too Large Exception
    private function fileTooLargeException($exception)
    {
        return (new BaseResponse([
            'status' => 'error',
            'message' => 'File Too Large',
            'errors' => ['upload' => 'The uploaded file exceeds the maximum allowed size.'],
            'code' => Response::HTTP_REQUEST_ENTITY_TOO_LARGE,
        ]))->response()->setStatusCode(Response::HTTP_REQUEST_ENTITY_TOO_LARGE);
    }

    // Handle File Not Found Exception
    private function fileNotFoundException($exception)
    {
        return (new BaseResponse([
            'status' => 'error',
            'message' => 'File Not Found',
            'errors' => ['file' => 'The requested file was not found.'],
            'code' => Response::HTTP_NOT_FOUND,
        ]))->response()->setStatusCode(Response::HTTP_NOT_FOUND);
    }

    // Handle Service Unavailable Exception
    private function serviceUnavailableException($exception)
    {
        return (new BaseResponse([
            'status' => 'error',
            'message' => 'Service Unavailable',
            'errors' => ['service' => $exception->getMessage()],
            'code' => Response::HTTP_SERVICE_UNAVAILABLE,
        ]))->response()->setStatusCode(Response::HTTP_SERVICE_UNAVAILABLE);
    }

    // Handle Generic HTTP Exception
    private function httpException($exception)
    {
        return (new BaseResponse([
            'status' => 'error',
            'message' => $exception->getMessage(),
            'code' => $exception->getStatusCode(),
        ]))->response()->setStatusCode($exception->getStatusCode());
    }

    // Default case for unhandled exceptions
    private function defaultException($exception)
    {
        return (new BaseResponse([
            'status' => 'error',
            'message' => 'Server Error',
            'errors' => ['server' => $this->getErrorMessage($exception)],
            'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
        ]))->response()->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
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
