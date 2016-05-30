<?php

namespace App\Http\Controllers;

use App\Config;
use App\Post;
use Cache;
use Carbon\Carbon;
use Request;

class RankingController extends Controller
{
    /**
     * Get daily ranking posts.
     *
     * @return \Illuminate\View\View
     */
    public function daily()
    {
        return $this->posts(1);
    }

    /**
     * Get weekly ranking posts.
     *
     * @return \Illuminate\View\View
     */
    public function weekly()
    {
        return $this->posts(7);
    }

    /**
     * Get monthly ranking posts.
     *
     * @return \Illuminate\View\View
     */
    public function monthly()
    {
        return $this->posts(30);
    }

    /**
     * @param int $days
     *
     * @return \Illuminate\View\View
     */
    protected function posts($days)
    {
        $key = 'ranking-'.$days.'-page-'.intval(Request::input('page', 1));

        $posts = Cache::remember($key, 5, function () use ($days) {
            return Post::where('published_at', '>=', Carbon::now()->subDays($days))
                ->whereNotNull('fbid')
                ->orderBy('ranks', 'desc')
                ->latest('published_at')
                ->paginate(5, ['fbid']);
        });

        $pageId = Config::getConfig('facebook-service')['page_id'];

        return view('ranking', compact('days', 'posts', 'pageId'));
    }
}
