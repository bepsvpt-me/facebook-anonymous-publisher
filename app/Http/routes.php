<?php

use Illuminate\Routing\Router;

/* @var Router $router */

$router->group(['middleware' => ['installed']], function (Router $router) {
    $router->get('/', ['as' => 'home', 'uses' => 'HomeController@home']);

    $router->post('kobe', ['as' => 'kobe', 'uses' => 'KobeController@kobe']);
    $router->post('kobe-non-secure', ['as' => 'kobe.non-secure', 'uses' => 'KobeController@kobe']);

    $router->group(['prefix' => 'ranking', 'as' => 'ranking.'], function (Router $router) {
        $router->get('daily', ['as' => 'daily', 'uses' => 'RankingController@daily']);
        $router->get('weekly', ['as' => 'weekly', 'uses' => 'RankingController@weekly']);
        $router->get('monthly', ['as' => 'monthly', 'uses' => 'RankingController@monthly']);
    });

    $router->group(['prefix' => 'dashboard', 'namespace' => 'Dashboard', 'as' => 'dashboard.', 'middleware' => 'auth'], function (Router $router) {
        $router->group(['prefix' => 'posts', 'as' => 'posts.'], function (Router $router) {
            $router->get('/', ['as' => 'index', 'uses' => 'PostController@index']);
            $router->get('{id}/block', ['as' => 'block', 'uses' => 'PostController@block']);
            $router->get('{id}/delete', ['as' => 'delete', 'uses' => 'PostController@destroy']);
        });

        $router->group(['prefix' => 'block-words', 'as' => 'block-words.'], function (Router $router) {
            $router->get('/', ['as' => 'index', 'uses' => 'BlockWordController@index']);
            $router->post('/', ['as' => 'store', 'uses' => 'BlockWordController@store']);
            $router->get('{value}/delete', ['as' => 'delete', 'uses' => 'BlockWordController@destroy']);
        });
    });
});

$router->group(['prefix' => 'install', 'as' => 'install.', 'middleware' => ['installed:false']], function (Router $router) {
    $router->get('/', ['as' => 'index', 'uses' => 'InstallController@index']);

    $router->get('facebook-service', ['as' => 'facebook', 'uses' => 'InstallController@facebook']);
    $router->post('facebook-service', ['as' => 'facebook.store', 'uses' => 'InstallController@storeFacebook']);

    $router->get('recaptcha-service', ['as' => 'recaptcha', 'uses' => 'InstallController@recaptcha']);
    $router->post('recaptcha-service', ['as' => 'recaptcha.store', 'uses' => 'InstallController@storeRecaptcha']);

    $router->get('application-service', ['as' => 'application', 'uses' => 'InstallController@application']);
    $router->post('application-service', ['as' => 'application.store', 'uses' => 'InstallController@storeApplication']);

    $router->get('finish', ['as' => 'finish', 'uses' => 'InstallController@finish']);
});
