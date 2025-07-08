<?php

namespace MasudZaman\LaravelApiResponse;

use Illuminate\Support\ServiceProvider;

/**
 * Service Provider for LaravelApiResponse
 */
class LaravelApiResponseServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register helper functions
        if (file_exists($helper = __DIR__ . '/Helpers/helper.php')) {
            require_once $helper;
        }
    }

    public function boot()
    {
        // Load localization files
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'api-response');
    }
}
