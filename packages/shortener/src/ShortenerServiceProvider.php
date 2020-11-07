<?php

namespace FacebookAnonymousPublisher\Shortener;

use Illuminate\Support\ServiceProvider;

class ShortenerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/../routes/web.php';
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/shortener.php' => config_path('shortener.php'),
            ]);

            $this->loadMigrationsFrom(__DIR__.'/../migrations');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/shortener.php', 'shortener'
        );

        $this->app->singleton('shortener', function ($app) {
            return new ShortenerManager($app);
        });

        $this->app->singleton('shortener.store', function ($app) {
            return $app['shortener']->driver();
        });
    }
}
