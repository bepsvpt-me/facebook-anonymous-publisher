<?php

namespace FacebookAnonymousPublisher\Shortener\Drivers;

use FacebookAnonymousPublisher\Shortener\Models\Shortener;

class Hashids extends Base
{
    /**
     * @var \Hashids\Hashids
     */
    protected $hashids;

    /**
     * Constructor.
     *
     * @param string $salt
     * @param int $length
     * @param string $alphabet
     */
    public function __construct($salt, $length, $alphabet)
    {
        $this->hashids = new \Hashids\Hashids($salt, $length, $alphabet);
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
        $short = $this->exists($url);

        if (false !== $short) {
            return $short;
        }

        return route('shortener.show', [
            'url' => $this->hashids->encode($this->save($url)),
        ]);
    }

    /**
     * Returns shorten url if exists, or false.
     *
     * @param $url
     *
     * @return string
     */
    protected function exists($url)
    {
        $shortener = Shortener::where('hash', hash('sha512', $url))->first();

        if (is_null($shortener)) {
            return false;
        }

        return route('shortener.show', [
            'url' => $this->hashids->encode($shortener->getKey()),
        ]);
    }

    /**
     * Get the origin url.
     *
     * @param string $short
     *
     * @return string
     */
    public function decode($short)
    {
        $ids = $this->hashids->decode($short);

        if (empty($ids)) {
            throw new \InvalidArgumentException;
        }

        return Shortener::findOrFail(reset($ids))->getAttribute('url');
    }
}
