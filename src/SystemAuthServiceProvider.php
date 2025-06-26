<?php

namespace Nk\SystemAuth;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Configuration\Middleware;
use Nk\SystemAuth\Http\Middleware\EnsureKeyVerified;
use Nk\SystemAuth\Http\Middleware\EnsurePackagePresent;

class SystemAuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/views', 'system-auth');
        $this->app['router']->aliasMiddleware('key.verified', EnsureKeyVerified::class);
        $this->app['router']->aliasMiddleware('package.present',EnsurePackagePresent::class);

        // Register middleware automatically
        $this->registerMiddleware();
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
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
    protected function registerMiddleware()
    {
        // Laravel 12 approach - register middleware during application configuration
        $this->app->afterResolving('middleware', function ($middleware) {
            if (method_exists($middleware, 'append')) {
                $middleware->append(EnsureKeyVerified::class);
                $middleware->append(EnsurePackagePresent::class);
            }
        });

        // Alternative approach - register during boot
        // $this->app->booted(function () {
        //     $app = $this->app;
        //     if ($app->has('middleware.stack')) {
        //         $middlewareStack = $app->make('middleware.stack');
        //         $middlewareStack[] = EnsureKeyVerified::class;
        //         $middlewareStack[] = EnsurePackagePresent::class;
        //     }
        // });
    }
}