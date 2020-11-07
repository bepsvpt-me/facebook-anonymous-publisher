<?php

namespace FacebookAnonymousPublisher\Shortener\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Shortener extends Model
{
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
    protected $fillable = ['hash', 'url', 'short', 'created_at'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $model) {
            $model->setAttribute('created_at', Carbon::now());
        });
    }
}
