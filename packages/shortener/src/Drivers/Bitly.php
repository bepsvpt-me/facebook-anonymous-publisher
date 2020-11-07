<?php

namespace FacebookAnonymousPublisher\Shortener\Drivers;

class Bitly extends Base
{
    /**
     * @var \GabrielKaputa\Bitly\Bitly
     */
    protected $bitly;

    /**
     * Constructor.
     *
     * @param string $token
     */
    public function __construct($token)
    {
        $this->bitly = \GabrielKaputa\Bitly\Bitly::withGenericAccessToken($token);
    }

    /**
     * Shorten the given url.
     *
     * @param string $url
     *
     * @return string
     */
    public function shorten($url)
    {
        $short = $this->bitly->shortenUrl($url);

        $this->save($url, $short);

        return $short;
    }
}
