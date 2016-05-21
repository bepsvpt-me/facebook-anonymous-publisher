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

if (! function_exists('file_build_path')) {
    /**
     * Get the path according to os.
     *
     * @return string
     */
    function file_build_path()
    {
        return implode(DIRECTORY_SEPARATOR, func_get_args());
    }
}
