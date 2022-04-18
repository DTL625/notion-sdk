<?php

namespace DTL\NotionApi;

use Illuminate\Support\ServiceProvider;

/**
 * Class LaravelNotionApiServiceProvider.
 */
class LaravelNotionApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('notion.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'notion');

        $this->app->singleton(Notion::class, function () {
            return new Notion(config('notion.api-token'), config('notion.version'));
        });
    }
}
