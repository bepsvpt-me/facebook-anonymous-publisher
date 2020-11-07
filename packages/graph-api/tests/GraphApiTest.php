<?php

use Mockery as m;

class PhotosTesting
{
    public function getDecodedBody()
    {
        static $count = 1;

        return ['id' => $count++];
    }
}

class GraphApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var FacebookAnonymousPublisher\GraphApi\GraphApi
     */
    protected $api;

    public function setUp()
    {
        parent::setUp();

        putenv('FACEBOOK_APP_ID=1');
        putenv('FACEBOOK_APP_SECRET=2');

        $this->api = new FacebookAnonymousPublisher\GraphApi\GraphApi;
    }

    public function tearDown()
    {
        m::close();
    }

    public function test_status()
    {
        $fb = m::mock('StdClass');

        $fb->shouldReceive('post')->once()->andReturn(1);

        $this->assertSame(1, $this->api->setFb($fb)->status('hi')->getResponse());
    }

    public function test_photo()
    {
        $fb = m::mock('StdClass');

        $fb->shouldReceive('post')->once()->andReturn(2);

        $this->assertSame(2, $this->api->setFb($fb)->photo(__DIR__.'/image.jpg')->getResponse());
    }

    public function test_photos()
    {
        $fb = m::mock('StdClass');

        $fb->shouldReceive('post')->times(4)->andReturn(new PhotosTesting);

        $photos = [__DIR__.'/image.jpg', __DIR__.'/image.jpg', __DIR__.'/image.jpg'];

        $body = $this->api->setFb($fb)->photos($photos)->getResponse()->getDecodedBody();

        $this->assertArrayHasKey('id', $body);
        $this->assertSame(4, $body['id']);
    }

    public function test_get_id_if_not_set_response()
    {
        $this->assertFalse($this->api->getId());
    }

    public function test_get_id_with_status()
    {
        $response = m::mock('StdClass');

        $response->shouldReceive('getDecodedBody')->once()->andReturn(['id' => '1_2']);

        $this->assertSame(['id' => '1', 'fbid' => '2'], $this->api->setResponse($response)->getId());
    }

    public function test_get_id_with_photo()
    {
        $response = m::mock('StdClass');

        $response->shouldReceive('getDecodedBody')->once()->andReturn(['id' => '3', 'post_id' => '3_4']);

        $this->assertSame(['id' => '3', 'fbid' => '4'], $this->api->setResponse($response)->getId());
    }

    public function test_get_id_others()
    {
        $response = m::mock('StdClass');

        $response->shouldReceive('getDecodedBody')->once()->andReturn([]);

        $this->assertFalse($this->api->setResponse($response)->getId());
    }
}
