<?php

namespace FacebookAnonymousPublisher\Wordfilter\Replace;

class Normal implements Replace
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
    public static function replace(array $words, $replacement, $text)
    {
        $replace = (mb_strlen($replacement) > 1)
            ? $replacement
            : static::transformReplacement($words, $replacement);

        return str_replace($words, $replace, $text);
    }

    /**
     * Get words replacement.
     *
     * @param array $words
     * @param string $replace
     *
     * @return array
     */
    protected static function transformReplacement(array $words, $replace)
    {
        return array_map(function ($value) use ($replace) {
            return str_repeat($replace, mb_strlen($value));
        }, $words);
    }
}
