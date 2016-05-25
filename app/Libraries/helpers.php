<?php

if (! function_exists('isBlockIp')) {
    /**
     * Check the current request ip is in block list.
     *
     * @return bool
     */
    function is_block_ip()
    {
        $ips = Cache::rememberForever('blacklist-ip', function () {
            return \App\Block::where('type', 'ip')->get(['value'])->pluck('value')->toArray();
        });

        return in_array(real_ip(Request::instance()), $ips, true);
    }
}

if (! function_exists('real_ip')) {
    /**
     * Get user real ip if the application is behind CloudFlare.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    function real_ip(Illuminate\Http\Request $request)
    {
        static $cloudFlareIps = [
            '103.21.244.0/22', '103.22.200.0/22', '103.31.4.0/22',
            '104.16.0.0/12', '108.162.192.0/18', '131.0.72.0/22',
            '141.101.64.0/18', '162.158.0.0/15', '172.64.0.0/13',
            '173.245.48.0/20', '188.114.96.0/20', '190.93.240.0/20',
            '197.234.240.0/22', '198.41.128.0/17', '199.27.128.0/21',
        ];

        static $ip = null;

        if (! is_null($ip)) {
            return $ip;
        }

        $ip = $request->ip();

        if (\Symfony\Component\HttpFoundation\IpUtils::checkIp($ip, $cloudFlareIps)) {
            $ip = $request->header('cf-connecting-ip', $request->header('x-forwarded-for', $ip));
        }

        return $ip;
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
