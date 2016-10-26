<?php

namespace App\Http\Middleware;

use Closure;
use FacebookAnonymousPublisher\Firewall\Firewall;
use Log;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class BlockBlacklistIp
{
    /**
     * @var Firewall
     */
    protected $firewall;

    /**
     * Constructor.
     *
     * @param Firewall $firewall
     */
    public function __construct(Firewall $firewall)
    {
        $this->firewall = $firewall;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $this->firewall->isAllowCountry(['TW', 'SG']) ||
            ($this->firewall->isBanned() && is_null($request->user()))
        ) {
            Log::notice('blacklist-ip', ['ip' => $this->firewall->ip()]);

            throw new AccessDeniedHttpException;
        }

        return $next($request);
    }
}
