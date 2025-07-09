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
        // Load localization files if any
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'api-response');

        // Publish config file
        $this->publishes([
            __DIR__.'/../config/api-response.php' => config_path('api-response.php'),
        ], 'config');
    }
}
