<?php

namespace App\Http\Controllers\Dashboard;

use App\Config;
use App\Http\Controllers\Controller;
use Redirect;

class WebsiteController extends Controller
{
    /**
     * Get the website manage page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('dashboard.website');
    }

    /**
     * Reset the website to allow to install again.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset()
    {
        Config::destroy('installed');

        return Redirect::route('install.index');
    }
}
