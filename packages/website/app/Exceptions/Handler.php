<?php

namespace App\Exceptions;

use Exception;
use FacebookAnonymousPublisher\Firewall\Firewall;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Log;
use Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        TokenMismatchException::class,
        ValidationException::class,
    ];

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
        parent::__construct(app());

        $this->firewall = $firewall;
    }

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        if ($e instanceof TokenMismatchException) {
            Log::notice('Token-Mismatch', [
                'ip' => $this->firewall->ip(),
                'user_agent' => Request::header('user-agent'),
            ]);
        }

        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        return parent::render($request, $e);
    }
}
