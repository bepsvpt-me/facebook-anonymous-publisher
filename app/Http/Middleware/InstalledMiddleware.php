<?php

namespace App\Http\Middleware;

use App\Config;
use Closure;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class InstalledMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param null $installed
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $installed = null)
    {
        $result = Config::getConfig('installed');

        $installed = is_null($installed);

        if ($installed && ! $result) {
            throw new ServiceUnavailableHttpException;
        } elseif (! $installed && $result) {
            throw new BadRequestHttpException;
        }

        return $next($request);
    }
}
