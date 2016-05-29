<?php

namespace App\Console\Commands\Facebook;

use App\Post;
use App\Shortener;
use GabrielKaputa\Bitly\Bitly;
use GuzzleHttp\Client;

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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $posts = Post::whereNotNull('fbid')
            ->where('published_at', '>=', $this->now->copy()->subDays(1))
            ->orderBy('ranks', 'desc')
            ->take(5)
            ->get(['id', 'fbid']);

        foreach ($posts as $index => $post) {
            $url = $this->getShortenUrl("https://www.facebook.com/{$this->config['page_id']}/posts/{$post->getAttribute('fbid')}");

            $urls[] = 'Top'.($index + 1).' : '.$url;
        }

        (new Client())->post(route('kobe.non-secure'), [
            'form_params' => [
                'content' => $this->now->toDateString().' 本日 Top 5'.PHP_EOL.implode(PHP_EOL, $urls ?? []),
                'color' => '000000',
                'accept-license' => true,
                'nolink' => true,
                'scheduling-auth' => config('services.bitly.token'),
            ],
        ]);
    }

    /**
     * Shorten the url.
     *
     * @param string $url
     *
     * @return bool|string
     */
    protected function getShortenUrl($url)
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
}
