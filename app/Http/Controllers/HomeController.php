<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    /**
     * Home page.
     *
     * @return \Illuminate\View\View
     */
    public function home()
    {
        return view('home');
    }

    /**
     * Get the terms of service and privacy policy view.
     *
     * @return \Illuminate\View\View
     */
    public function tosAndPp()
    {
        return view('tos-pp');
    }
}
