<?php

namespace FacebookAnonymousPublisher\Wordfilter\Match;

interface Match
{
    /**
     * Determines the given text contain any of words.
     *
     * @param string $text
     * @param array $words
     *
     * @return bool
     */
    public static function match($text, array $words);
}
