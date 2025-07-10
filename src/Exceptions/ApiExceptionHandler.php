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
        // Check if the request is for an API (expectsJson)
        if ($request->expectsJson()) {

            // Handle NotFoundHttpException (non-existent API route)
            if ($exception instanceof NotFoundHttpException) {
                $errorResponse = [
                    'success' => false,
                    'status' => 'error',
                    'code' => 404,
                    'message' => __('The requested route could not be found.'),
                    'data' => null,
                    'errors' => [
                        'route' => [__('The route you are looking for does not exist.')]
                    ],
                    'meta' => null,
                    'locale' => app()->getLocale(),
                ];

                return (new BaseResponse($errorResponse))
                        ->response()
                        ->setStatusCode(404);
            }

            // Handle UnauthorizedException (API permission denied)
            if ($exception instanceof UnauthorizedException) {
                $errorResponse = [
                    'success' => false,
                    'status' => 'error',
                    'code' => 403,
                    'message' => __('You do not have access to do that.'),
                    'data' => null,
                    'errors' => [
                        'permission' => [__('You do not have the required permission to access this resource.')]
                    ],
                    'meta' => null,
                    'locale' => app()->getLocale(),
                ];

                return (new BaseResponse($errorResponse))
                        ->response()
                        ->setStatusCode(403);
            }

            // Handle ModelNotFoundException (API resource not found)
            if ($exception instanceof ModelNotFoundException) {
                $errorResponse = [
                    'success' => false,
                    'status' => 'error',
                    'code' => 404,
                    'message' => __('The requested resource was not found.'),
                    'data' => null,
                    'errors' => [
                        'resource' => [__('The resource you are looking for could not be found.')]
                    ],
                    'meta' => null,
                    'locale' => app()->getLocale(),
                ];

                return (new BaseResponse($errorResponse))
                        ->response()
                        ->setStatusCode(404);
            }

            // Handle general exception for API
            return parent::render($request, $exception);
        }

        // Handle web requests (does not expect JSON)
        return parent::render($request, $exception);
    }
}
