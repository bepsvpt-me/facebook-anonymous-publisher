<?php

namespace App\Http\Controllers;

use App\Block;
use App\Config;
use App\Http\Requests\KobeRequest;
use App\Post;
use Carbon\Carbon;
use Facebook\Facebook;
use Facebook\FacebookResponse;
use Facebook\FileUpload\FacebookFile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Overtrue\Pinyin\Pinyin;

class KobeController extends Controller
{
    /**
     * The application config.
     *
     * @var array|null
     */
    protected $application;

    /**
     * @var Facebook
     */
    protected $fb;

    /**
     * @var Post
     */
    protected $post;

    /**
     * Initialize the kobe.
     */
    protected function init()
    {
        $this->application = Config::getConfig('application-service');

        $this->fb = new Facebook(Config::getConfig('facebook-service'));

        $this->post = new Post;
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
        $this->init();

        $this->savePost($request);

        $this->posted($this->postFeed($request->file('image')));

        return redirect("https://www.facebook.com/{$this->post->getAttribute('fbid')}");
    }

    /**
     * Create post.
     *
     * @param Request $request
     *
     * @return bool
     */
    protected function savePost(Request $request)
    {
        $content = $this->filterBlockWords(
            $this->normalizeNewLine(
                $this->stripCharacters(
                    $request->input('content')
                )
            )
        );

        $this->post->setAttribute('content', $this->transformHashTag($content));
        $this->post->setAttribute('link', $this->findLink($content));
        $this->post->setAttribute('has_image', $request->hasFile('image'));
        $this->post->setAttribute('user_agent', $request->header('user-agent'));
        $this->post->setAttribute('ip', $request->ip());
        $this->post->setAttribute('created_at', Carbon::now());

        return $this->post->save();
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
        return str_replace([d('&lrm;')], '', $string);
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
    protected function newLines($multiplier = 1)
    {
        return str_repeat(PHP_EOL, $multiplier);
    }

    /**
     * Filter the block words.
     *
     * @param string $content
     *
     * @return string
     */
    protected function filterBlockWords($content)
    {
        $words = Block::where('type', 'keyword')->get();

        foreach ($words as $word) {
            $content = $this->replaceBlockWord($word, $content);
        }

        return $content;
    }

    /**
     * Replace block word with ♥ symbol.
     *
     * @param Block $word
     * @param string $content
     *
     * @return string
     */
    protected function replaceBlockWord(Block $word, $content)
    {
        $pinyin = new Pinyin();

        $blockWordLen = mb_strlen($word->getAttribute('value'));
        $blockWordPinyin = $pinyin->sentence($word->getAttribute('value'));

        $contentLen = mb_strlen($content) - $blockWordLen + 1;

        for ($i = 0; $i < $contentLen; ++$i) {
            $sub = mb_substr($content, $i, $blockWordLen);

            if ($pinyin->sentence($sub) === $blockWordPinyin) {
                $content = implode('', [
                    mb_substr($content, 0, $i),
                    str_repeat('♥', $blockWordLen),
                    mb_substr($content, $i + $blockWordLen),
                ]);

                $i += ($blockWordLen - 1);
            }
        }

        return $content;
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
            $post = Post::find($match, ['fbid']);

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
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|null $file
     *
     * @return FacebookResponse
     */
    protected function postFeed($file)
    {
        if (! is_null($file)) {
            return $this->postPhotos($file);
        }

        return $this->fb->post('/me/feed', [
            'message' => $this->content(),
            'link' => $this->post->getAttribute('link'),
        ]);
    }

    /**
     * Post a photo.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return FacebookResponse
     */
    protected function postPhotos($file)
    {
        $file = $file->move(
            storage_path(file_build_path('app', 'images', intval(floor($this->post->getKey() / 5000)))),
            $this->post->getKey().'.'.$file->guessExtension()
        );

        return $this->fb->post('/me/photos', [
            'source' => new FacebookFile($file->getPathname()),
            'caption' => $this->content(),
        ]);
    }

    /**
     * Get post content.
     *
     * @return string
     */
    protected function content()
    {
        return implode('', [
            // Page hash tag
            '#'.$this->application['page_name'].$this->post->getKey(),
            $this->newLines(1),

            // Link that redirect to the kobe page
            '發文請至：'.route('redirect', ['rand' => Str::quickRandom(8)]),
            $this->newLines(1),

            // Extra content that should insert to the post
            $this->application['extra_content'],
            $this->newLines(2),

            // User post content
            $this->post->getAttribute('content'),
            $this->newLines(2),

            // Post submitted time
            'Submitted At: '.$this->post->getAttribute('created_at'),
        ]);
    }

    /**
     * Save fbid and published_at.
     *
     * @param FacebookResponse $response
     *
     * @return bool
     */
    protected function posted(FacebookResponse $response)
    {
        $key = $this->post->getAttribute('has_image') ? 'post_id' : 'id';

        $this->post->setAttribute('fbid', substr(strstr($response->getDecodedBody()[$key], '_'), 1));
        $this->post->setAttribute('published_at', Carbon::now());

        return $this->post->save();
    }
}
