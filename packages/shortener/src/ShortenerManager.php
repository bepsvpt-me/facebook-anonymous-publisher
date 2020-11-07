<?php

namespace FacebookAnonymousPublisher\Shortener;

use FacebookAnonymousPublisher\Shortener\Drivers\Base;
use FacebookAnonymousPublisher\Shortener\Drivers\Bitly;
use FacebookAnonymousPublisher\Shortener\Drivers\Hashids;
use InvalidArgumentException;

class ShortenerManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Create a new Cache manager instance.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Get a shortener driver instance.
     *
     * @param string|null $driver
     *
     * @return Base
     */
    public function driver($driver = null)
    {
        $shortener = $this->app['config']['shortener'];

        $driver = $driver ?: $shortener['default'];

        $driverMethod = 'create'.ucfirst($driver).'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($shortener);
        }

        throw new InvalidArgumentException("Driver [{$driver}] is not supported.");
    }

    /**
     * Create bilty driver.
     *
     * @param array $config
     *
     * @return Bitly
     */
    protected function createBitlyDriver(array $config)
    {
        $bitly = $config['bitly'];

        return new Bitly($bitly['token']);
    }

    /**
     * Create hashids driver.
     *
     * @param array $config
     *
     * @return Hashids
     */
    protected function createHashidsDriver(array $config)
    {
        $hashids = $config['hashids'];

        return new Hashids($hashids['salt'], $hashids['length'], $hashids['alphabet']);
    }
}
