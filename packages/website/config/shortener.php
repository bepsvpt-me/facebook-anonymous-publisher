<?php

return [

    /*
     * Shortener Driver
     *
     * Supported: "hashids", "bitly"
     */

    'default' => env('SHORTENER_DRIVER', 'hashids'),

    /*
     * Bitly Access Token
     */

    'bitly' => [
        'token' => env('BITLY_TOKEN'),
    ],

    /*
     * Hashids: http://hashids.org/php/
     */

    'hashids' => [
        'salt' => env('HASHIDS_SALT', ''),
        'length' => env('HASHIDS_MIN_LENGTH', 6),
        'alphabet' => env('HASHIDS_ALPHABET', ''),
    ],

];
