<?php

namespace FacebookAnonymousPublisher\Shortener\Facades;

use Illuminate\Support\Facades\Facade;

class Shortener extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'shortener.store';
    }
}
