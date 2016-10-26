<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Redirect;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $role
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $role = null)
    {
        if (Auth::guest()) {
            return Redirect::route('auth.sign-in');
        } elseif (! is_null($role) && ! Auth::user()->own($role)) {
            throw new AccessDeniedHttpException;
        }

        return $next($request);
    }
}
