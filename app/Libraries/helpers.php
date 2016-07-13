<?php

use Vectorface\Whip\Whip;

if (! function_exists('is_support_country')) {
    /**
     * Check the current request ip is support country.
     *
     * @return bool
     */
    function is_support_country()
    {
        $reader = new \GeoIp2\Database\Reader(config('services.geoip2.path'));

        try {
            $isoCode = $reader->country(real_ip())->country->isoCode;
        } catch (Exception $e) {
            return true;
        }

        return 'TW' === $isoCode || 'SG' === $isoCode;
    }
}

if (! function_exists('is_block_ip')) {
    /**
     * Check the current request ip is in block list.
     *
     * @param bool $checkSignIn
     *
     * @return bool
     */
    function is_block_ip($checkSignIn = true)
    {
        if ($checkSignIn && ! is_null(Request::user())) {
            return false;
        }

        $ips = Cache::rememberForever('blacklist-ip', function () {
            return \App\Block::where('type', 'ip')->get(['value'])->pluck('value')->toArray();
        });

        return in_array(real_ip(), $ips, true);
    }
}

if (! function_exists('real_ip')) {
    /**
     * Get user real ip if the application is behind CloudFlare.
     *
     * @return string
     */
    function real_ip()
    {
        static $ip = null;

        if (! is_null($ip)) {
            return $ip;
        }

        $whip = new Whip(Whip::CLOUDFLARE_HEADERS | Whip::REMOTE_ADDR, [
            Whip::CLOUDFLARE_HEADERS => [
                Whip::IPV4 => config('cloudflare.ipv4'),
                Whip::IPV6 => config('cloudflare.ipv6'),
            ],
        ]);

        return $ip = $whip->getValidIpAddress();
    }
}

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
