<?php

namespace App\Console\Commands\Facebook;

use App\Config;
use Carbon\Carbon;
use Facebook\Facebook;
use FacebookAnonymousPublisher\GraphApi\GraphApi;
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
     * @var GraphApi
     */
    protected $graphApi;

    /**
     * @var Carbon
     */
    protected $now;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->now = Carbon::now();

        $this->config = Config::getConfig('facebook-service');

        if (is_null($this->config)) {
            throw new \RuntimeException('Invalid facebook service config.');
        }

        $this->graphApi = new GraphApi($this->config);

        $this->fb = new Facebook($this->config);
    }
}
