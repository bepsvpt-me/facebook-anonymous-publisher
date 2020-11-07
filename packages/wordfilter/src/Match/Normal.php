<?php

namespace FacebookAnonymousPublisher\Wordfilter\Match;

class Normal implements Match
{
    /**
     * Determines the given text contain any of words.
     *
     * @param string $text
     * @param array $words
     *
     * @return bool
     */
    public static function match($text, array $words)
    {
        foreach ($words as $word) {
            if (false !== mb_strpos($text, $word)) {
                return true;
            }
        }

        return false;
    }
}
