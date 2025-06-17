<?php

namespace Nk\SystemAuth;

use Illuminate\Support\ServiceProvider;

class SystemAuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'system-auth-migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/views', 'system-auth');

        // Register middleware
        $this->app['router']->aliasMiddleware('key.verified', \Nk\SystemAuth\Http\Middleware\EnsureKeyVerified::class);
        $this->app['router']->aliasMiddleware('package.present', \Nk\SystemAuth\Http\Middleware\EnsurePackagePresent::class);
    }

    public function register()
    {
        // Bind any services if needed
        $this->loadHelpers();
    }
    
    protected function loadHelpers()
    {
        foreach (glob(__DIR__ . '/helpers.php') as $filename) {
            require_once $filename;
        }
    }
}