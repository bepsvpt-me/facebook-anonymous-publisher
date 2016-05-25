<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Redirect;
use Socialite;

class OAuthController extends Controller
{
    /**
     * Redirect the user to the Facebook authentication page.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function facebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from Facebook.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function facebookCallback()
    {
        Socialite::driver('facebook')->user();

        return Redirect::route('home');
    }
}
