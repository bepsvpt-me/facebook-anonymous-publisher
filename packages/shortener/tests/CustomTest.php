<?php

class CustomTest extends Base
{
    public function test_custom_driver()
    {
        $driver = new CustomDriver;

        $shortener = new \FacebookAnonymousPublisher\Shortener\Shortener($driver);

        $shortener->shorten('https://apple.com');
        $shortener->shorten('https://live.apple.com');

        $this->assertCount(2, \FacebookAnonymousPublisher\Shortener\Models\Shortener::all());
    }
}
