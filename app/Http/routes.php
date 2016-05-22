<?php

use Illuminate\Routing\Router;

/* @var Router $router */

$router->get('/', ['as' => 'home', 'uses' => 'HomeController@home']);

$router->get('r/{rand}', ['as' => 'redirect', 'uses' => 'HomeController@redirect']);

$router->post('kobe', ['as' => 'kobe', 'uses' => 'KobeController@kobe']);

$router->group(['prefix' => 'dashboard', 'namespace' => 'Dashboard', 'as' => 'dashboard.', 'middleware' => 'auth'], function (Router $router) {
    $router->get('/', ['as' => 'index', 'uses' => 'PostController@index']);
    $router->get('{id}/delete', ['as' => 'delete', 'uses' => 'PostController@destroy']);
});

$router->group(['prefix' => 'install', 'as' => 'install.'], function (Router $router) {
    $router->get('/', ['as' => 'index', 'uses' => 'InstallController@index']);

    $router->get('facebook-service', ['as' => 'facebook', 'uses' => 'InstallController@facebook']);
    $router->post('facebook-service', ['as' => 'facebook.store', 'uses' => 'InstallController@storeFacebook']);

    $router->get('recaptcha-service', ['as' => 'recaptcha', 'uses' => 'InstallController@recaptcha']);
    $router->post('recaptcha-service', ['as' => 'recaptcha.store', 'uses' => 'InstallController@storeRecaptcha']);

    $router->get('application-service', ['as' => 'application', 'uses' => 'InstallController@application']);
    $router->post('application-service', ['as' => 'application.store', 'uses' => 'InstallController@storeApplication']);

    $router->get('finish', ['as' => 'finish', 'uses' => 'InstallController@finish']);
});
