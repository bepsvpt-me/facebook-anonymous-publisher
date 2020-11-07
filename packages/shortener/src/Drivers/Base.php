<?php

namespace FacebookAnonymousPublisher\Shortener\Drivers;

use FacebookAnonymousPublisher\Shortener\Models\Shortener;

abstract class Base
{
    /**
     * Save record to database.
     *
     * @param string $origin
     * @param string|null $short
     *
     * @return mixed
     */
    protected function save($origin, $short = null)
    {
        $shortener = Shortener::updateOrCreate(
            ['hash' => hash('sha512', $origin)],
            ['url' => $origin, 'short' => $short]
        );

        return $shortener->exists ? $shortener->getKey() : false;
    }

    /**
     * Shorten the given url.
     *
     * @param string $url
     *
     * @return string
     */
    abstract public function shorten($url);
}
