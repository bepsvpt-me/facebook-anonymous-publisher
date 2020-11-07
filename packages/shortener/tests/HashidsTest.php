<?php

class HashidsTest extends Base
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
        putenv('SHORTENER_DRIVER=hashids');

        parent::setUp();

        $this->shortener = app('shortener.store');

        $this->model = new FacebookAnonymousPublisher\Shortener\Models\Shortener;
    }

    public function test_shorten()
    {
        $this->shortener->shorten('https://www.google.com');
        $this->shortener->shorten('https://www.google.com.tw');
        $this->shortener->shorten('https://apple.com');

        $this->assertCount(3, $this->model->all());
    }

    public function test_duplicate()
    {
        $short1 = $this->shortener->shorten('https://www.google.com');
        $short2 = $this->shortener->shorten('https://www.google.com');
        $short3 = $this->shortener->shorten('https://www.google.com');

        $this->assertCount(1, $this->model->all());

        $this->assertSame($short1, $short2);
        $this->assertSame($short2, $short3);
    }

    public function test_redirect()
    {
        $url = 'https://www.google.com';

        $short = $this->shortener->shorten($url);

        $this->get($short)->assertRedirectedTo($url);

        $short .= 'apple';

        $this->get($short)->assertResponseStatus(500);
    }

    public function test_facade()
    {
        S::shorten('https://www.google.com');

        $this->assertCount(1, $this->model->all());
    }
}
