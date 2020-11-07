<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Redirect;

class AuthController extends Controller
{
    use ThrottlesLogins;

    /**
     * The maximum number of login attempts for delaying further attempts.
     *
     * @var int
     */
    protected $maxLoginAttempts = 3;

    /**
     * The number of seconds to delay further login attempts.
     *
     * @var int
     */
    protected $lockoutTime = 300;

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
        if ($this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        if (! Auth::attempt($request->only(['username', 'password']), true)) {
            $this->incrementLoginAttempts($request);

            return back()->withInput()->withErrors(['sign-in' => trans('auth.failed')]);
        }

        return Redirect::home();
    }

    /**
     * Sign out the application.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function signOut()
    {
        Auth::logout();

        return Redirect::home();
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    protected function username()
    {
        return '';
    }
}
