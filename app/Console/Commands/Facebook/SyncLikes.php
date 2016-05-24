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

        $size = count($likes);

        for ($i = 0; $i < $size; $i += 2) {
            if (200 !== $likes[$i]['code'] || 200 !== $likes[$i+1]['code']) {
                Log::notice('facebook-sync-likes', [
                    'fbid' => $posts[$i >> 1]->getAttribute('fbid'),
                    'code' => [
                        'likes' => $likes[$i]['code'],
                        'comments' => $likes[$i+1]['code'],
                    ],
                ]);
            } else {
                $ranking = json_decode($likes[$i]['body'], true)['summary']['total_count'] * 2
                    + json_decode($likes[$i+1]['body'], true)['summary']['total_count'];

                $posts[$i >> 1]->update([
                    'likes' => $ranking,
                    'sync_at' => $this->now,
                ]);
            }
        }

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

        $objects = [
            'likes?summary=true&limit=1',
            'comments?summary=true&limit=1',
        ];

        foreach ($posts as $post) {
            foreach ($objects as $object) {
                $requests[] = new FacebookRequest(
                    null,
                    null,
                    'GET',
                    "/{$this->config['page_id']}_{$post->getAttribute('fbid')}/{$object}"
                );
            }
        }

        return $requests;
    }
}
