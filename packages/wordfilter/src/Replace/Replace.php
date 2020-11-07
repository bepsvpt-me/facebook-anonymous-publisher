<?php

namespace FacebookAnonymousPublisher\Wordfilter\Replace;

interface Replace
{
    /**
     * Replace the text with replacement according to the given words.
     *
     * @param array $words
     * @param string $replacement
     * @param string $text
     *
     * @return string
     */
    public static function replace(array $words, $replacement, $text);
}
