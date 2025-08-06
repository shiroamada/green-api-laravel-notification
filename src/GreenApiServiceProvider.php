<?php

namespace NotificationChannels\GreenApi;

use Illuminate\Support\ServiceProvider;

class GreenApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(GreenApi::class, function () {
            $config = config('services.green_api');

            return new GreenApi($config);
        });
    }

    public function boot()
    {
        // Boot method for Laravel 11+ compatibility
    }
}
