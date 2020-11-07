<?php

namespace FacebookAnonymousPublisher\Wordfilter;

class Wordfilter
{
    /**
     * Determines the given text contain any of words.
     *
     * @param string $text
     * @param array $words
     *
     * @return bool
     */
    public function match($text, array $words)
    {
        if (! $this->isHan($text)) {
            return $this->matchExact($text, $words);
        }

        return Match\Chinese::match($text, $words);
    }

    /**
     * Determines the given text contain any of words.
     *
     * @param string $text
     * @param array $words
     *
     * @return bool
     */
    public function matchExact($text, array $words)
    {
        return Match\Normal::match($text, $words);
    }

    /**
     * Replace text with replacement using given words.
     *
     * @param array $words
     * @param string $replace
     * @param string $text
     *
     * @return string
     */
    public function replace(array $words, $replace, $text)
    {
        if (! $this->isHan($text)) {
            return $this->replaceExact($words, $replace, $text);
        }

        return Replace\Chinese::replace($words, $replace, $text);
    }

    /**
     * Replace text with replacement using given words.
     *
     * @param array $words
     * @param string $replace
     * @param string $text
     *
     * @return string
     */
    public function replaceExact(array $words, $replace, $text)
    {
        return Replace\Normal::replace($words, $replace, $text);
    }

    /**
     * Determines the given text contain chinese words.
     *
     * @param string $text
     *
     * @return bool
     */
    protected function isHan($text)
    {
        return boolval(preg_match('/\p{Han}+/uU', $text));
    }
}
