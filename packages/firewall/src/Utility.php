<?php

namespace FacebookAnonymousPublisher\Firewall;

use GeoIp2\Database\Reader;
use IPTools\IP;
use IPTools\Network;
use IPTools\Range;
use Vectorface\Whip\Whip;

class Utility
{
    /**
     * Cloudflare ip list.
     *
     * @var array
     */
    const CLOUDFLARE_IP = [
        Whip::IPV4 => [
            '103.21.244.0/22',
            '103.22.200.0/22',
            '103.31.4.0/22',
            '104.16.0.0/12',
            '108.162.192.0/18',
            '131.0.72.0/22',
            '141.101.64.0/18',
            '162.158.0.0/15',
            '172.64.0.0/13',
            '173.245.48.0/20',
            '188.114.96.0/20',
            '190.93.240.0/20',
            '197.234.240.0/22',
            '198.41.128.0/17',
        ],
        Whip::IPV6 => [
            '2400:cb00::/32',
            '2405:8100::/32',
            '2405:b500::/32',
            '2606:4700::/32',
            '2803:f800::/32',
            '2c0f:f248::/32',
            '2a06:98c0::/29',
        ],
    ];

    /**
     * Geoip country database path.
     *
     * @var string
     */
    const GEO_DB = __DIR__.'/../db/GeoLite2-Country.mmdb';

    /**
     * Get real ip address.
     *
     * @return false|string
     */
    public static function ip()
    {
        $whip = new Whip(
            Whip::CLOUDFLARE_HEADERS | Whip::REMOTE_ADDR,
            [
                Whip::CLOUDFLARE_HEADERS => self::CLOUDFLARE_IP,
            ]
        );

        return $whip->getValidIpAddress();
    }

    /**
     * Get the ip address iso code.
     *
     * @param string $ip
     *
     * @return null|string
     */
    public static function isoCode($ip)
    {
        return (new Reader(self::GEO_DB))
            ->country($ip)
            ->country
            ->isoCode;
    }

    /**
     * Get CIDR.
     *
     * @param string $ip
     *
     * @return string
     */
    public static function cidr($ip)
    {
        return Network::parse($ip)->getCIDR();
    }

    /**
     * Check if ip is within range.
     *
     * @param string $cidr
     * @param string $ip
     *
     * @return bool
     */
    public static function contains($cidr, $ip)
    {
        $cidr = Range::parse($cidr);

        $ip = new IP($ip);

        if ($cidr->getFirstIP()->getVersion() !== $ip->getVersion()) {
            return false;
        }

        return $cidr->contains($ip);
    }
}
