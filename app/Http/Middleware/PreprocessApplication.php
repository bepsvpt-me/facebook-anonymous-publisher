<?php

namespace App\Http\Middleware;

use App;
use App\Block;
use App\Config;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Jenssegers\Agent\Facades\Agent;
use Log;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PreprocessApplication
{
    /**
     * The view factory implementation.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;

    /**
     * PreprocessApplication constructor.
     *
     * @param ViewFactory $view
     */
    public function __construct(ViewFactory $view)
    {
        $this->view = $view;
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
        $this->blockBlacklistIp($request->ip(), $request->header('x-forwarded-for'));

        $this->setLocale();

        // Set the cookie https only if the connection is secure.
        config(['session.secure' => $request->secure()]);

        // Share the application service to all views.
        $application = Config::getConfig('application-service');

        if (! is_null($application)) {
            $this->view->share('pageName', $application['page_name']);
        }

        return $next($request);
    }

    /**
     * Block black list ips.
     *
     * @param string $userIp
     * @param string $forwardedIp
     */
    protected function blockBlacklistIp($userIp, $forwardedIp)
    {
        $ips = Block::where('type', 'ip')->get();

        foreach ($ips as $ip) {
            if (in_array($ip->getAttribute('value'), [$userIp, $forwardedIp], true)) {
                Log::info('blacklist-ip', ['ip' => $userIp, 'forwardedIp' => $forwardedIp]);

                throw new AccessDeniedHttpException;
            }
        }
    }

    /**
     * Set Carbon and App locale.
     *
     * @return void
     */
    protected function setLocale()
    {
        foreach (Agent::languages() as $lang) {
            if (str_contains($lang, '-')) {
                list($l, $e) = explode('-', $lang);

                $lang = $l.'-'.mb_strtoupper($e);
            }

            if (file_exists($this->carbonLangPath($lang))) {
                Carbon::setLocale($lang);

                App::setLocale($lang);

                break;
            }
        }
    }

    /**
     * Get the Carbon lang src path.
     *
     * @param string $lang
     *
     * @return string
     */
    protected function carbonLangPath($lang)
    {
        return base_path(file_build_path('vendor', 'nesbot', 'carbon', 'src', 'Carbon', 'Lang', $lang.'.php'));
    }
}
