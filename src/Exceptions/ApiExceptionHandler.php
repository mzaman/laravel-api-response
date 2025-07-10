<?php

namespace MasudZaman\LaravelApiResponse\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use MasudZaman\LaravelApiResponse\Http\Resources\BaseResponse;
use Throwable;

class ApiExceptionHandler extends ExceptionHandler
{
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
            'success' => false,
            'status' => 'error',
            'code' => 500, // Default error code
            'message' => $this->getErrorMessage($exception),
            'data' => null,
            'errors' => [
                'exception' => [get_class($exception)],
                'message' => $exception->getMessage(),
            ],
            'meta' => null,
            'locale' => app()->getLocale(),
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

        // Handle NotFoundHttpException (non-existent API route)
        if ($exception instanceof NotFoundHttpException) {
            $errorResponse['code'] = 404;
            $errorResponse['message'] = $errorResponse['message'] ?? __('The requested route could not be found.');
            $errorResponse['errors'] = [
                'route' => [__('The route you are looking for does not exist.')]
            ];
            return (new BaseResponse($errorResponse))
                    ->response()
                    ->setStatusCode(404);
        }

        // Handle UnauthorizedException (API permission denied)
        if ($exception instanceof UnauthorizedException) {
            $errorResponse['code'] = 403;
            $errorResponse['message'] = $errorResponse['message'] ?? __('You do not have access to do that.');
            $errorResponse['errors'] = [
                'permission' => [__('You do not have the required permission to access this resource.')]
            ];
            return (new BaseResponse($errorResponse))
                    ->response()
                    ->setStatusCode(403);
        }

        // Handle ModelNotFoundException (API resource not found)
        if ($exception instanceof ModelNotFoundException) {
            $errorResponse['code'] = 404;
            $errorResponse['message'] = $errorResponse['message'] ?? __('The requested resource was not found.');
            $errorResponse['errors'] = [
                'resource' => [__('The resource you are looking for could not be found.')]
            ];
            return (new BaseResponse($errorResponse))
                    ->response()
                    ->setStatusCode(404);
        }

        // Handle general exception for API
        return parent::render($request, $exception);
    }

    /**
     * Get the error message based on the exception.
     *
     * @param  \Throwable  $exception
     * @return string
     */
    private function getErrorMessage(Throwable $exception)
    {
        // Check if the exception has a specific message
        if (!empty($exception->getMessage()) && $exception->getMessage() !== 'Not Found') {
            return $exception->getMessage();
        }

        // Default message for unknown or generic exceptions
        return __('An unexpected error occurred.');
    }
}
