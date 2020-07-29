<?php

namespace Devcake\LaravelLokiLogging;

use Illuminate\Support\ServiceProvider;

class L3ServiceProvider extends ServiceProvider
{
    public const LOG_LOCATION = 'logs/loki.log';

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/l3config.php' => config_path('l3config.php')
        ], 'laravel-loki-logging');
        $this->commands([
            L3Persister::class
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/l3config.php',
            'l3'
        );
    }
}
