<?php

namespace App\Console\Commands\Facebook;

use App\Post;
use App\User;
use Facebook\FacebookRequest;
use Log;

class SyncRanks extends FacebookCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:sync-ranks';

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
        $posts = $this->getPosts();

        $collection = $this->fb->sendBatchRequest($this->prepareRequests($posts))->getDecodedBody();

        $size = count($collection);

        for ($i = 0; $i < $size; $i += 2) {
            if ($this->isLogFails($posts[$i >> 1]->getAttribute('fbid'), $collection[$i]['code'], $collection[$i + 1]['code'])) {
                $posts[$i >> 1]->delete();
            } else {
                $posts[$i >> 1]->update([
                    'ranks' => $this->getRanks($collection[$i]['body'], $collection[$i + 1]['body']),
                    'sync_at' => $this->now,
                ]);
            }
        }

        Log::info('facebook-sync-ranks');

        $this->info('Sync facebook ranks success!');
    }

    /**
     * Get posts that need to sync.
     *
     * @return array
     */
    protected function getPosts()
    {
        $count = User::where('username', 'like', 'facebook-%')->count();

        $nums = min(25, 5 + $count * 10);

        return Post::whereNotNull('fbid')
            ->where('published_at', '>=', $this->now->copy()->subMonth())
            ->oldest('sync_at')
            ->take($nums)
            ->get(['id', 'ranks', 'fbid', 'sync_at']);
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
                    null, null, 'GET',
                    "/{$this->config['page_id']}_{$post->getAttribute('fbid')}/{$object}"
                );
            }
        }

        return $requests;
    }

    /**
     * Log if the response code are not all 200.
     *
     * @param int $fbid
     * @param int $likesCode
     * @param int $commentsCode
     *
     * @return bool
     */
    protected function isLogFails($fbid, $likesCode, $commentsCode)
    {
        if (count(array_diff([$likesCode, $commentsCode], [200])) === 0) {
            return false;
        }

        Log::notice('facebook-sync-likes', [
            'fbid' => $fbid,
            'code' => [
                'likes' => $likesCode,
                'comments' => $commentsCode,
            ],
        ]);

        return true;
    }

    /**
     * Get the post ranks.
     *
     * @param string $likes
     * @param string $comments
     *
     * @return int
     */
    protected function getRanks($likes, $comments)
    {
        $likes = json_decode($likes, true)['summary']['total_count'];

        $comments = json_decode($comments, true)['summary']['total_count'];

        return intval(floatval($likes) * 1.2 + $comments);
    }
}
