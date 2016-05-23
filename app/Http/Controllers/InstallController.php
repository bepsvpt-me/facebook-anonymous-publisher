<?php

namespace App\Http\Controllers;

use App\Config;
use App\Http\Requests\Install\ApplicationRequest;
use App\Http\Requests\Install\FacebookRequest;
use App\Http\Requests\Install\RecaptchaRequest;
use App\User;
use Artisan;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Redirect;

class InstallController extends Controller
{
    /**
     * Just redirect.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        return Redirect::route('install.facebook');
    }

    /**
     * Facebook install page.
     *
     * @return \Illuminate\View\View
     */
    public function facebook()
    {
        $service = 'facebook';

        return view('install.form', compact('service'));
    }

    /**
     * Store facebook config and redirect to recaptcha install page.
     *
     * @param FacebookRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeFacebook(FacebookRequest $request)
    {
        return $this->storeConfig(
            'facebook-service',
            $request->only([
                'app_id', 'app_secret', 'default_graph_version', 'default_access_token', 'page_id',
            ]),
            'install.recaptcha'
        );
    }

    /**
     * Recaptcha install page.
     *
     * @return \Illuminate\View\View
     */
    public function recaptcha()
    {
        $service = 'recaptcha';

        return view('install.form', compact('service'));
    }

    /**
     * Store recaptcha config and redirect to application install page.
     *
     * @param RecaptchaRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeRecaptcha(RecaptchaRequest $request)
    {
        return $this->storeConfig(
            'recaptcha-service',
            $request->only(['public_key', 'private_key']),
            'install.application'
        );
    }

    /**
     * Application install page.
     *
     * @return \Illuminate\View\View
     */
    public function application()
    {
        $service = 'application';

        return view('install.form', compact('service'));
    }

    /**
     * Store application config and redirect to finish page.
     *
     * @param ApplicationRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeApplication(ApplicationRequest $request)
    {
        User::updateOrCreate(['username' => $request->input('username')], [
            'password' => bcrypt($request->input('password')),
        ]);

        return $this->storeConfig(
            'application-service',
            $request->only([
                'page_name', 'extra_content', 'license',
                'ga', 'ad-client', 'ad-slot',
            ]),
            'install.finish'
        );
    }

    /**
     * Install finish page.
     *
     * @return \Illuminate\View\View
     */
    public function finish()
    {
        Config::updateOrCreate(['key' => 'installed', 'value' => true]);

        Artisan::call('cache:clear');

        return view('install.finish');
    }

    /**
     * Store config and redirect to next page.
     *
     * @param string $key
     * @param mixed $value
     * @param string $nextRoute
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function storeConfig($key, $value, $nextRoute)
    {
        $config = Config::updateOrCreate(['key' => $key], ['value' => $value]);

        if (! $config->exists) {
            throw new ModelNotFoundException;
        }

        return Redirect::route($nextRoute);
    }
}
