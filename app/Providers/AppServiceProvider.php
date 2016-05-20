<?php

namespace App\Providers;

use App\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->overrideConfig();
    }

    /**
     * Override the original application and service config.
     *
     * @return void
     */
    protected function overrideConfig()
    {
        $this->overrideRecaptcha();
    }

    /**
     * Using config store in database to override the environment config.
     *
     * @return void
     */
    protected function overrideRecaptcha()
    {
        $recaptcha = Config::getConfig('recaptcha-service');

        if (! is_null($recaptcha)) {
            config([
                'recaptcha.public_key' => $recaptcha['public_key'],
                'recaptcha.private_key' => $recaptcha['private_key'],
            ]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
