<?php

namespace App\Http\Controllers;

use App\User;
use Auth;
use Flash;
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
        try {
            $user = User::firstOrCreate([
                'username' => 'facebook-'.Socialite::driver('facebook')->user()->getId(),
            ]);

            Auth::login($user);
        } catch (\Exception $e) {
            Flash::error('登入失敗');
        }

        return Redirect::home();
    }
}
