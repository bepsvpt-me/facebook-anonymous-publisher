<?php

namespace App\Http\Middleware;

use App\Config;
use Closure;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class InstalledMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $installed = Config::find('installed');

        $installed = is_null($installed) ? false : boolval($installed->getAttribute('value'));

        if ($request->is('install*') === $installed) {
            throw new ServiceUnavailableHttpException;
        }

        return $next($request);
    }
}
