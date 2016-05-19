<?php

namespace App\Http\Controllers;

use App\Http\Requests\KobeRequest;
use App\Post;
use Carbon\Carbon;
use Facebook\Facebook;
use Facebook\FacebookResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Home page.
     *
     * @return \Illuminate\View\View
     */
    public function home()
    {
        return view('home');
    }

    /**
     * Post kobe.
     *
     * @param KobeRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function kobe(KobeRequest $request)
    {
        $post = $this->savePost($request);

        $fb = new Facebook(config('services.facebook'));

        $response = $fb->post('/me/feed/', [
            'message' => $this->content($post),
            'link' => $post->getAttribute('link'),
        ]);

        $post = $this->posted($response, $post);

        return redirect("https://www.facebook.com/{$post->getAttribute('fbid')}");
    }

    /**
     * Create post.
     *
     * @param Request $request
     *
     * @return Post
     */
    protected function savePost(Request $request)
    {
        $post = new Post();

        $content = $this->normalizeNewLine($this->stripCharacters($request->input('content')));

        $post->setAttribute('content', $this->transformHashTag($content));
        $post->setAttribute('link', $this->findLink($content));
        $post->setAttribute('user_agent', $request->header('user-agent'));
        $post->setAttribute('ip', $request->ip());
        $post->setAttribute('created_at', Carbon::now());

        $post->save();

        return $post;
    }

    /**
     * Strip special characters.
     *
     * @param string $string
     *
     * @return string
     */
    protected function stripCharacters($string)
    {
        return str_replace(d('&lrm;'), '', $string);
    }

    /**
     * Normalize new line symbol.
     *
     * @param string $content
     *
     * @return string
     */
    protected function normalizeNewLine($content)
    {
        $content = str_replace(["\r\n", "\r", "\n"], $this->newLines(1), $content);

        while (str_contains($content, $this->newLines(3))) {
            $content = str_replace($this->newLines(3), $this->newLines(2), $content);
        }

        return $content;
    }

    /**
     * Get specific amount of new lines.
     *
     * @param int $multiplier
     *
     * @return string
     */
    protected function newLines($multiplier)
    {
        return str_repeat(PHP_EOL, $multiplier);
    }

    /**
     * Transform hash tag to more powerful.
     *
     * @param string $content
     *
     * @return string
     */
    protected function transformHashTag($content)
    {
        if (0 === preg_match_all('/#靠北民雄大鳳梨(\d+)/', $content, $matches)) {
            return $content;
        }

        $stack = [];

        foreach ($matches[1] as $index => $match) {
            $post = Post::find($match);

            if (is_null($post) || in_array($post->getAttribute('fbid'), $stack)) {
                continue;
            }

            $content = $this->addLinkToHashTag($matches[0][$index], $post->getAttribute('fbid'), $content);

            $stack[] = $post->getAttribute('fbid');
        }

        return $content;
    }

    /**
     * Append link to hash tag.
     *
     * @param string $hashTag
     * @param int $fbid
     * @param string $content
     *
     * @return mixed
     */
    protected function addLinkToHashTag($hashTag, $fbid, $content)
    {
        return str_replace(
            $hashTag,
            "{$hashTag} (https://www.facebook.com/{$fbid})",
            $content
        );
    }

    /**
     * Find links in content.
     *
     * @param $content
     *
     * @return null|string
     */
    protected function findLink($content)
    {
        $amount = preg_match_all(
            '/\b(https?):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|]/i',
            $content,
            $matches
        );

        if (0 === $amount) {
            return;
        }

        return $matches[0][0];
    }

    /**
     * Get post content.
     *
     * @param Post $post
     *
     * @return string
     */
    protected function content(Post $post)
    {
        $content = '#靠北民雄大鳳梨'.$post->getKey()." \n\n";

        $content .= $post->getAttribute('content')."\n\n";

        $content .= 'Submitted At: '.$post->getAttribute('created_at');

        return $content;
    }

    /**
     * @param FacebookResponse $response
     * @param Post $post
     *
     * @return Post
     */
    protected function posted(FacebookResponse $response, Post $post)
    {
        list($pageId, $fbid) = explode('_', $response->getDecodedBody()['id']);

        $post->setAttribute('fbid', $fbid);

        $post->save();

        return $post;
    }
}
