<?php

namespace App\Console\Commands;

use App\Config;
use App\Post;
use Carbon\Carbon;
use Facebook\Facebook;
use Facebook\FacebookRequest;
use Illuminate\Console\Command;
use Log;

class SyncFacebookLikes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:sync-likes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        if (! is_null($this->config)) {
            $this->fb = new Facebook($this->config);
        }

        $this->now = Carbon::now();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (is_null($this->fb)) {
            $this->error('Facebook service not set.');

            return;
        }

        $posts = Post::whereNotNull('fbid')
            ->whereNotNull('published_at')
            ->where('published_at', '>=', $this->now->copy()->subWeek())
            ->oldest('sync_at')
            ->take(25)
            ->get(['id', 'likes', 'fbid', 'sync_at']);

        $likes = $this->fb->sendBatchRequest($this->prepareRequests($posts))->getDecodedBody();

        $result = [];

        foreach ($likes as $index => $like) {
            $success = false;

            if (200 === $like['code']) {
                $info = json_decode($like['body'], true);

                $posts[$index]->update([
                    'likes' => $info['summary']['total_count'],
                    'sync_at' => $this->now,
                ]);

                $success = true;
            }

            $result[$posts[$index]->getAttribute('fbid')] = $success;
        }

        Log::info('facebook-sync-likes', $result);

        $this->info('Sync facebook likes success!');
    }

    /**
     * Prepare the facebook batch request.
     *
     * @param array $posts
     *
     * @return array
     */
    protected function prepareRequests($posts)
    {
        $requests = [];

        foreach ($posts as $post) {
            $requests[] = new FacebookRequest(
                null,
                null,
                'GET',
                "/{$this->config['page_id']}_{$post->getAttribute('fbid')}/likes?summary=true&limit=1"
            );
        }

        return $requests;
    }
}
