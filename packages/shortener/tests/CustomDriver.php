<?php

class CustomDriver extends FacebookAnonymousPublisher\Shortener\Drivers\Base
{
    /**
     * Shorten the given url.
     *
     * @param string $url
     *
     * @return string
     */
    public function shorten($url)
    {
        $short = 'https://example.com/'.substr(md5($url), 0, 7);

        $this->save($url, $short);

        return $short;
    }
}
