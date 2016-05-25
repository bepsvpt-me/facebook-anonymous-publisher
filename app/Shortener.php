<?php

namespace App;

use Carbon\Carbon;
use Hashids\Hashids;

class Shortener extends \Eloquent
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shorteners';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['hash'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at'];

    /**
     * Generate the short url.
     *
     * @param string $url
     *
     * @return string
     */
    public static function shorten($url)
    {
        $short = self::firstOrNew(['hash' => hash('sha512', $url)]);

        if (! $short->exists) {
            $short->setAttribute('url', $url);
            $short->setAttribute('created_at', Carbon::now());

            $short->save();
        }

        return route('short-url', ['hash' => (new Hashids('', 6))->encode($short->getKey())]);
    }
}
