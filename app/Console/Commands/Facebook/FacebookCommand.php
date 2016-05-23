<?php

namespace App\Console\Commands\Facebook;

use App\Config;
use Carbon\Carbon;
use Facebook\Facebook;
use Illuminate\Console\Command;

abstract class FacebookCommand extends Command
{
    /**
     * @var array|null
     */
    protected $config;

    /**
     * @var Facebook|null
     */
    protected $fb;

    /**
     * @var Carbon
     */
    protected $now;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->config = Config::getConfig('facebook-service');

        if (is_null($this->config)) {
            throw new \RuntimeException;
        }

        $this->fb = new Facebook($this->config);
        
        $this->now = Carbon::now();
    }
}
