<?php

namespace App\Http\Controllers;

use App\Config;
use Redirect;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class HomeController extends Controller
{
    /**
     * Home page.
     *
     * @return \Illuminate\View\View
     */
    public function home()
    {
        if (! Config::getConfig('installed')) {
            throw new ServiceUnavailableHttpException;
        }

        return view('home', [
            'application' => Config::getConfig('application-service'),
        ]);
    }

    /**
     * Just redirect to home page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect()
    {
        return Redirect::route('home');
    }
}
