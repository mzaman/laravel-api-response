<?php

namespace MasudZaman\LaravelApiResponse\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Http\JsonResponse success($data = [], $message = 'Request was successful', $statusCode = 200)
 * @method static \Illuminate\Http\JsonResponse error($message = 'Something went wrong', $statusCode = 500, $errors = [])
 */
class ApiResponse extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'api-response'; // This matches the binding in the service provider
    }
}
