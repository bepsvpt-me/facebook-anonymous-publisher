<?php

namespace App\Http\Middleware;

use Closure;
use Log;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class BlockBlacklistIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! is_support_country() || is_block_ip()) {
            Log::notice('blacklist-ip', ['ip' => real_ip($request)]);

            throw new AccessDeniedHttpException;
        }

        return $next($request);
    }
}
