<?php

namespace MasudZaman\LaravelApiResponse;

use Illuminate\Support\ServiceProvider;

class LaravelApiResponseServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register the helpers
        if (file_exists($helper = __DIR__.'/Helpers/helper.php')) {
            require_once $helper;
        }
    }

    public function boot()
    {
        // Publish configuration, migrations, etc.
    }
}
