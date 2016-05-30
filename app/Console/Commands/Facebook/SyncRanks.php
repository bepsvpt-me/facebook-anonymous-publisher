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

        $i = 0;

        foreach ($collection as $item) {
            if ($this->isLogFails($posts[$i]->getAttribute('fbid'), $item['code'])) {
                $posts[$i]->delete();
            } else {
                $ranks = $this->getRanks($item['body']);

                $posts[$i]->update([
                    'ranks' => $ranks['ranks'],
                    'ranks_data' => $ranks,
                    'sync_at' => $this->now,
                ]);
            }

            ++$i;
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
        $count = User::where('username', 'like', 'facebook-%')->count(['id']);

        $nums = min(50, 1 + $count);

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

        foreach ($posts as $post) {
            $requests[] = new FacebookRequest(
                null, null, 'GET',
                "/{$this->config['page_id']}_{$post->getAttribute('fbid')}?fields=comments.summary(true).limit(0),likes.summary(true).limit(0)"
            );
        }

        return $requests;
    }

    /**
     * Log if the response code are not all 200.
     *
     * @param int $fbid
     * @param int $code
     *
     * @return bool
     */
    protected function isLogFails($fbid, $code)
    {
        if (200 === $code) {
            return false;
        }

        Log::notice('facebook-sync-likes', [
            'fbid' => $fbid,
            'code' => $code,
        ]);

        return true;
    }

    /**
     * Get the post ranks.
     *
     * @param string $body
     *
     * @return int
     */
    protected function getRanks($body)
    {
        $data = json_decode($body, true);

        $likes = $data['likes']['summary']['total_count'];

        $comments = $data['comments']['summary']['total_count'];

        return [
            'likes' => $likes,
            'comments' => $comments,
            'ranks' => intval($likes + $comments * 8.7),
        ];
    }
}
