<?php

namespace FacebookAnonymousPublisher\Shortener;

use FacebookAnonymousPublisher\Shortener\Drivers\Base;

class Shortener
{
    /**
     * @var Base
     */
    protected $driver;

    /**
     * Constructor.
     *
     * @param Base $driver
     */
    public function __construct(Base $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Shorten the given url.
     *
     * @param string $url
     *
     * @return string
     */
    public function shorten($url)
    {
        return $this->driver->shorten($url);
    }
}
