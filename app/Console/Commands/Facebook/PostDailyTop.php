<?php

namespace App\Console\Commands\Facebook;

use App\Post;
use App\Shortener;
use Crypt;
use GabrielKaputa\Bitly\Bitly;
use GuzzleHttp\Client;
use Log;

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
        $posts = $this->posts();

        if (false === $posts) {
            return;
        }

        foreach ($posts as $index => $post) {
            $url = $this->shortenUrl("https://www.facebook.com/{$this->config['page_id']}/posts/{$post->getAttribute('fbid')}");

            $urls[] = 'Top'.($index + 1).' : '.$url;
        }

        $this->send($urls ?? []);
    }

    /**
     * Get the daily top posts.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function posts()
    {
        try {
            return Post::whereNotNull('fbid')
                ->where('published_at', '>=', $this->now->copy()->subDays(1))
                ->orderBy('ranks', 'desc')
                ->take(5)
                ->get(['id', 'fbid']);
        } catch (\PDOException $e) {
            Log::error('database-connection-refused');

            return false;
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
        if (is_null(config('services.bitly.token'))) {
            return $this->shortenUsingLocal($url);
        }

        return $this->shortenUsingBitly($url);
    }

    /**
     * Shorten the url using local shortener.
     *
     * @param string $url
     *
     * @return string
     */
    protected function shortenUsingLocal($url)
    {
        return Shortener::shorten($url);
    }

    /**
     * Shorten the url using bitly.
     *
     * @param string $url
     *
     * @return bool|string
     */
    protected function shortenUsingBitly($url)
    {
        static $bitly = null;

        if (is_null($bitly)) {
            $bitly = Bitly::withGenericAccessToken(config('services.bitly.token'));
        }

        return $bitly->shortenUrl($url);
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
        if (empty($urls)) {
            return;
        }

        (new Client())->post(route('kobe.non-secure'), [
            'form_params' => [
                'content' => $this->now->toDateString().' 本日 Top 5'.PHP_EOL.implode(PHP_EOL, $urls),
                'color' => '000000',
                'accept-license' => true,
                'nolink' => true,
                'scheduling-auth' => Crypt::encrypt('daily-top'),
            ],
        ]);
    }
}
