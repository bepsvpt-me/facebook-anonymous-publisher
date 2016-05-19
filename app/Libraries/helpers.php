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
