<?php

if (! function_exists('d')) {
    /**
     * Convert all HTML entities to their applicable characters.
     *
     * @param  string  $value
     * @return string
     */
    function d($value)
    {
        return html_entity_decode($value, ENT_QUOTES, 'UTF-8');
    }
}

if (! function_exists('normalizeNewLine')) {
    /**
     * Normalize new line character.
     *
     * @param string $text
     *
     * @return string
     */
    function normalizeNewLine($text)
    {
        $text = str_replace(["\r\n", "\r", "\n"], PHP_EOL, $text);

        while (str_contains($text, PHP_EOL.PHP_EOL.PHP_EOL)) {
            $text = str_replace(PHP_EOL.PHP_EOL.PHP_EOL, PHP_EOL.PHP_EOL, $text);
        }

        return $text;
    }
}
