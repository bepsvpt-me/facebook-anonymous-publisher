<?php

class BitlyTest extends Base
{
    /**
     * @var \FacebookAnonymousPublisher\Shortener\Drivers\Base
     */
    protected $shortener;

    /**
     * @var \FacebookAnonymousPublisher\Shortener\Models\Shortener
     */
    protected $model;

    public function setUp()
    {
        putenv('SHORTENER_DRIVER=bitly');

        parent::setUp();

        $this->shortener = app('shortener.store');

        $this->model = new FacebookAnonymousPublisher\Shortener\Models\Shortener;
    }

    public function test_shorten()
    {
    }
}
