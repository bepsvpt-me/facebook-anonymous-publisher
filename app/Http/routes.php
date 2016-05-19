<?php

use Illuminate\Routing\Router;

/** @var Router $router */

$router->get('/', 'HomeController@home');

$router->post('kobe', ['as' => 'kobe', 'uses' => 'HomeController@kobe']);
