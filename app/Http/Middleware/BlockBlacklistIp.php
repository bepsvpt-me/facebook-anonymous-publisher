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
        $allow = true;

        $banned = $this->firewall->isBanned();

        if (! $this->firewall->isAllowCountry(['TW'])) {
            $allow = false;
        } elseif ('permanent' === $banned) {
            $allow = false;
        } elseif (false !== $banned && is_null($request->user())) {
            $allow = false;
        }

        if (! $allow) {
            Log::notice('blacklist-ip', [
                'ip' => $this->firewall->ip(),
                'type' => $banned,
            ]);

            throw new AccessDeniedHttpException;
        }

        return $next($request);
    }
}
