<?php

namespace App\Console\Commands\Facebook;

use App\Post;
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
            ->where('published_at', '>=', $this->now->copy()->subDays(2))
            ->orderBy('ranks', 'desc')
            ->take(5)
            ->get(['id', 'fbid']);

        $bitly = Bitly::withGenericAccessToken(config('services.bitly.token'));

        foreach ($posts as $index => $post) {
            $url = $bitly->shortenUrl("https://www.facebook.com/{$this->config['page_id']}/posts/{$post->getAttribute('fbid')}");

            $urls[] = 'Top'.($index + 1).' : '.$url;
        }

        (new Client())->post(route('kobe.non-secure'), [
            'form_params' => [
                'content' => $this->now->toDateString().' Top 5'.PHP_EOL.implode(PHP_EOL, $urls ?? []),
                'accept-license' => true,
                'nolink' => true,
                'scheduling-auth' => config('services.bitly.token'),
            ],
        ]);
    }
}
