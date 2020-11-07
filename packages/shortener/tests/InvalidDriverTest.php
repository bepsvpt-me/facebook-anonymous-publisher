<?php

class InvalidDriverTest extends Base
{
    public function setUp()
    {
        putenv('SHORTENER_DRIVER=xxx');

        parent::setUp();
    }

    public function test_shorten()
    {
        $this->expectException(InvalidArgumentException::class);

        app('shortener.store');
    }
}
