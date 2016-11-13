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
    protected $description = 'Sync the ranks of facebook posts.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        parent::handle();

        $posts = $this->posts();

        if (false === $posts || $posts->isEmpty()) {
            return;
        }

        $items = $this->fb
            ->sendBatchRequest($this->prepareRequests($posts))
            ->getDecodedBody();

        foreach ($items as $index => $item) {
            if (! $this->isFail($posts[$index], $item['code'])) {
                $this->updateRanks($posts[$index], $item['body']);
            }
        }

        Log::info('facebook-sync-ranks');

        $this->info('Sync facebook ranks success!');
    }

    /**
     * Get posts that need to sync.
     *
     * @return \Illuminate\Database\Eloquent\Collection|bool
     */
    protected function posts()
    {
        try {
            $count = User::where('username', 'like', 'facebook-%')->count(['id']);

            $nums = min(50, 1 + $count);

            return Post::whereNotNull('fbid')
                ->where('published_at', '>=', $this->now->copy()->subMonth())
                ->oldest('sync_at')
                ->take($nums)
                ->get(['id', 'ranks', 'fbid', 'sync_at']);
        } catch (\PDOException $e) {
            Log::error('database-connection-refused');

            return false;
        }
    }

    /**
     * Prepare the facebook batch request.
     *
     * @param \Illuminate\Database\Eloquent\Collection $posts
     *
     * @return array
     */
    protected function prepareRequests($posts)
    {
        $queryString = 'fields=comments.summary(true).limit(0),likes.summary(true).limit(0),shares';

        $requests = [];

        foreach ($posts as $post) {
            $requests[] = new FacebookRequest(
                null, null, 'GET',
                "/{$this->config['page_id']}_{$post->getAttribute('fbid')}?{$queryString}"
            );
        }

        return $requests;
    }

    /**
     * Log and delete the post if the response code is not 200.
     *
     * @param Post $post
     * @param int $code
     *
     * @return bool
     */
    protected function isFail(Post $post, $code)
    {
        if (200 === $code) {
            return false;
        }

        Log::notice('facebook-sync-likes', [
            'fbid' => $post->getAttribute('fbid'),
            'code' => $code,
        ]);

        try {
            $post->delete();
        } catch (\PDOException $e) {
            Log::error('database-connection-refused');
        }

        return true;
    }

    /**
     * Update the ranks of the post.
     *
     * @param Post $post
     * @param string $body
     *
     * @return void
     */
    protected function updateRanks(Post $post, $body)
    {
        $ranks = $this->ranks($body);

        try {
            $post->update([
                'ranks' => $ranks['ranks'],
                'ranks_data' => $ranks,
                'sync_at' => $this->now,
            ]);
        } catch (\PDOException $e) {
            Log::error('database-connection-refused', [
                'schedule' => 'update post ranks',
                'fbid' => $post->getAttribute('fbid'),
            ]);
        }
    }

    /**
     * Get the post ranks.
     *
     * @param string $body
     *
     * @return array
     */
    protected function ranks($body)
    {
        $data = json_decode($body, true);

        list($likes, $comments, $shares) = [
            $data['likes']['summary']['total_count'],
            $data['comments']['summary']['total_count'],
            isset($data['shares']['count']) ? $data['shares']['count'] : 0,
        ];

        return [
            'likes' => $likes,
            'comments' => $comments,
            'shares' => $shares,
            'ranks' => intval($likes + $comments * 8.7 + $shares * 87),
        ];
    }
}
