<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Redirect;

class AuthController extends Controller
{
    /**
     * Get the sign in view.
     *
     * @return \Illuminate\View\View
     */
    public function signIn()
    {
        return view('sign-in');
    }

    /**
     * Auth the credentials.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function auth(Request $request)
    {
        if (! Auth::attempt($request->only(['username', 'password']), true)) {
            return back()->withInput()->withErrors(['sign-in' => trans('auth.failed')]);
        }

        return Redirect::home();
    }
}
