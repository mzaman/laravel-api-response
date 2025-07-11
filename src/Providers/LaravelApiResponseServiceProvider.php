<?php

namespace MasudZaman\LaravelApiResponse\Providers;

use Illuminate\Support\ServiceProvider;
use MasudZaman\LaravelApiResponse\Response\ApiResponse;

class LaravelApiResponseServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind the ApiResponse class to the container
        $this->app->singleton('api-response', function ($app) {
            return new ApiResponse();
        });
    }

    public function boot()
    {        
        // Load language files from the package
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'api');

        // Publish the language files
        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang'),
        ], 'lang');

    }
}
