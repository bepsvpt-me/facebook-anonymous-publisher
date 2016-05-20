<?php

namespace App\Http\Middleware;

use App\Config;
use Closure;
use Illuminate\Contracts\View\Factory as ViewFactory;

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
        $application = Config::getConfig('application-service');

        if (! is_null($application)) {
            $this->view->share('pageName', $application['page_name']);
        }

        return $next($request);
    }
}
