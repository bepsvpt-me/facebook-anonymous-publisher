<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Keys
    |--------------------------------------------------------------------------
    |
    | Set the public and private API keys as provided by reCAPTCHA.
    |
    | In version 2 of reCAPTCHA, public_key is the Site key,
    | and private_key is the Secret key.
    |
    */

    'public_key'     => env('RECAPTCHA_PUBLIC_KEY', ''),

    'private_key'    => env('RECAPTCHA_PRIVATE_KEY', ''),

];
