<?php

namespace App\Http\Controllers;

use App\Config;
use App\Http\Requests\KobeRequest;
use App\Post;
use Carbon\Carbon;
use Facebook\Facebook;
use Facebook\FacebookResponse;
use Facebook\FileUpload\FacebookFile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Redirect;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class HomeController extends Controller
{
    /**
     * The application config.
     *
     * @var array|null
     */
    protected $application;

    /**
     * HomeController constructor.
     */
    public function __construct()
    {
        if (! Config::getConfig('installed')) {
            throw new ServiceUnavailableHttpException;
        }

        $this->application = Config::getConfig('application-service');
    }

    /**
     * Home page.
     *
     * @return \Illuminate\View\View
     */
    public function home()
    {
        return view('home', [
            'application' => $this->application,
        ]);
    }

    /**
     * Just redirect to home page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect()
    {
        return Redirect::route('home');
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

        $fb = new Facebook(Config::getConfig('facebook-service'));

        if (! $request->hasFile('image')) {
            $response = $this->postFeed($fb, $post);
        } else {
            $response = $this->postPhotos($fb, $post, $request);
        }

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
        if (0 === preg_match_all('/#'.$this->application['page_name'].'(\d+)/', $content, $matches)) {
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
            return null;
        }

        return $matches[0][0];
    }

    /**
     * Post to feed.
     *
     * @param Facebook $fb
     * @param Post $post
     *
     * @return FacebookResponse
     */
    protected function postFeed(Facebook $fb, Post $post)
    {
        return $fb->post('/me/feed', [
            'message' => $this->content($post),
            'link' => $post->getAttribute('link'),
        ]);
    }

    /**
     * Post a photo.
     *
     * @param Facebook $fb
     * @param Post $post
     * @param Request $request
     *
     * @return FacebookResponse
     */
    protected function postPhotos(Facebook $fb, Post $post, Request $request)
    {
        return $fb->post('/me/photos', [
            'source' => new FacebookFile($request->file('image')->getPathname()),
            'caption' => $this->content($post),
        ]);
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
        return implode('', [
            // Page hash tag
            '#'.$this->application['page_name'].$post->getKey(),
            $this->newLines(1),

            // Link that redirect to the kobe page
            '發文請至：'.route('redirect', ['rand' => Str::quickRandom(8)]),
            $this->newLines(1),

            // Extra content that should insert to the post
            $this->application['extra_content'],
            $this->newLines(2),

            // User post content
            $post->getAttribute('content'),
            $this->newLines(2),

            // Post submitted time
            'Submitted At: '.$post->getAttribute('created_at'),
        ]);
    }

    /**
     * @param FacebookResponse $response
     * @param Post $post
     *
     * @return Post
     */
    protected function posted(FacebookResponse $response, Post $post)
    {
        $key = str_contains($response->getRequest()->getEndpoint(), 'photos') ? 'post_id' : 'id';

        list($pageId, $fbid) = explode('_', $response->getDecodedBody()[$key]);

        $post->setAttribute('fbid', $fbid);
        $post->setAttribute('published_at', Carbon::now());

        $post->save();

        return $post;
    }
}
