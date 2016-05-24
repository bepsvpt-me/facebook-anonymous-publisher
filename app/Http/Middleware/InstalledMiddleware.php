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
     * @param null|string $exceptInstalled
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $exceptInstalled = null)
    {
        if (boolval(Config::getConfig('installed')) !== is_null($exceptInstalled)) {
            throw new ServiceUnavailableHttpException;
        }

        return $next($request);
    }
}
