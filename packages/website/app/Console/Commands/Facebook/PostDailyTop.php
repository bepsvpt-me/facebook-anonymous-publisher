<?php

namespace App\Console\Commands\Facebook;

use App\Post;
use Log;
use Shortener;

class PostDailyTop extends FacebookCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:post-daily-top';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post the top five facebook posts link.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        parent::handle();

        foreach ($this->posts() as $index => $post) {
            $url = $this->shortenUrl("https://www.facebook.com/{$post->getAttribute('fbid')}");

            $urls[] = 'Top'.($index + 1).' : '.$url;
        }

        if (isset($urls)) {
            $this->send($urls);
        }
    }

    /**
     * Get the daily top posts.
     *
     * @return \Illuminate\Database\Eloquent\Collection|array
     */
    protected function posts()
    {
        try {
            return Post::whereNotNull('fbid')
                ->where('published_at', '>=', $this->now->copy()->subDays(1))
                ->orderBy('ranks', 'desc')
                ->take(5)
                ->get(['fbid']);
        } catch (\PDOException $e) {
            Log::error('database-connection-refused');

            return [];
        }
    }

    /**
     * Shorten the url.
     *
     * @param string $url
     *
     * @return bool|string
     */
    protected function shortenUrl($url)
    {
        return Shortener::shorten($url);
    }

    /**
     * Send the post request.
     *
     * @param array $urls
     *
     * @return void
     */
    protected function send($urls)
    {
        $content = implode(PHP_EOL, [
            $this->now->toDateString().' Top 5',
            implode(PHP_EOL, $urls),
        ]);

        $this->graphApi->status($content);
    }
}
