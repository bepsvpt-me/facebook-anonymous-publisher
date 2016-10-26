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
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->overrideConfig();

        $this->includeMacros();
    }

    /**
     * Override the original application and service config.
     *
     * @return void
     */
    protected function overrideConfig()
    {
        $this->overrideFacebook();
        $this->overrideRecaptcha();
    }

    /**
     * Using config store in database to override the environment config.
     *
     * @return void
     */
    protected function overrideFacebook()
    {
        $facebook = Config::getConfig('facebook-service');

        if (! is_null($facebook)) {
            config([
                'services.facebook.client_id' => $facebook['app_id'],
                'services.facebook.client_secret' => $facebook['app_secret'],
            ]);
        }
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
     * Include the form and html macros.
     *
     * @return void
     */
    protected function includeMacros()
    {
        require_once app_path('Libraries/form.php');
        require_once app_path('Libraries/html.php');
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
