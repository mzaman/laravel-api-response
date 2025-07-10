<?php

namespace MasudZaman\LaravelApiResponse\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Debug\ExceptionHandler;
use MasudZaman\LaravelApiResponse\Exceptions\ApiExceptionHandler;

class ApiExceptionHandlerServiceProvider extends ServiceProvider
{
    /**
     * Register the custom exception handler for APIs.
     *
     * @return void
     */
    public function register()
    {
        // Register the custom exception handler as a singleton, letting Laravel resolve the dependencies
        $this->app->singleton(ExceptionHandler::class, function ($app) {
            return new ApiExceptionHandler($app); // Pass the container to the constructor
        });
    }
}
