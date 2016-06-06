<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'bitly' => [
        'token' => env('BITLY_TOKEN'),
    ],

    'facebook' => [
        'client_id' => '',
        'client_secret' => '',
        'redirect' => env('APP_URL').'/oauth/facebook/callback',
    ],

    'geoip2' => [
        'path' => storage_path(file_build_path('app', 'geoip', 'GeoLite2-Country.mmdb')),
    ],

];
