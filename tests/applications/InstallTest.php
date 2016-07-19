<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;

class InstallTest extends TestCase
{
    use DatabaseMigrations;

    public function test_it_will_throw_service_unavailable_http_exception_if_not_install()
    {
        $this->get('/')->assertResponseStatus(503);
    }
}
