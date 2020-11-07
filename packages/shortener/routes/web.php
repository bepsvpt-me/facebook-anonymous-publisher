<?php

use Illuminate\Support\Facades\Route;

Route::get('s/{url}', [
    'as' => 'shortener.show',
    'uses' => 'FacebookAnonymousPublisher\Shortener\Controllers\ShortenerController@show',
]);
