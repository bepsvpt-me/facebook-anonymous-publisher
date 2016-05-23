<?php

namespace App\Console\Commands\Facebook;

use App\Post;
use Facebook\FacebookRequest;
use Log;

class SyncLikes extends FacebookCommand
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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $posts = Post::whereNotNull('fbid')
            ->where('published_at', '>=', $this->now->copy()->subMonth())
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
