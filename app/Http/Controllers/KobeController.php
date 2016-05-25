<?php

namespace App\Http\Controllers;

use App\Block;
use App\Config;
use App\Http\Requests\KobeRequest;
use App\Post;
use Cache;
use Carbon\Carbon;
use Facebook\Facebook;
use Facebook\FacebookResponse;
use Facebook\FileUpload\FacebookFile;
use Illuminate\Http\Request;
use Image;
use Intervention\Image\Gd\Font;
use Overtrue\Pinyin\Pinyin;

class KobeController extends Controller
{
    /**
     * The application config.
     *
     * @var array
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

        $file = $request->has('post-by-image') ? $this->canvas() : $request->file('image');

        $this->posted($this->postFeed($file));

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
            $this->stripCharacters(
                $this->normalizeNewLine(
                    $request->input('content')
                )
            )
        );

        $this->post->setAttribute('content', $this->transformHashTag($content));
        $this->post->setAttribute('link', $request->has('nolink') ? null : $this->findLink($content));
        $this->post->setAttribute('has_image', $request->has('post-by-image') || $request->hasFile('image'));
        $this->post->setAttribute('user_agent', $request->header('user-agent'));
        $this->post->setAttribute('ip', realIp($request));
        $this->post->setAttribute('created_at', Carbon::now());
        $this->post->setAttribute('sync_at', Carbon::now());

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
        $len = mb_strlen($string);

        $removes = [];

        for ($i = 0; $i < $len; ++$i) {
            $char = mb_substr($string, $i, 1);

            if ((1 === strlen($char)) && "\n" !== $char && ! in_array($char, $removes, true) && (! ctype_print($char) || ctype_cntrl($char))) {
                $removes[] = $char;
            }
        }

        return str_replace(array_merge($removes, [d('&lrm;')]), '', $string);
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
        foreach ($this->getBlockWords() as $word) {
            $content = str_replace($word, str_repeat('♥', mb_strlen($word)), $content, $count);

            if (0 === $count) {
                $content = $this->replaceBlockWord($this->transformBlockWord($word), $content);
            }
        }

        return $content;
    }

    /**
     * Get block words.
     *
     * @return array
     */
    protected function getBlockWords()
    {
        return Cache::remember('block-words', 30, function () {
            return Block::where('type', 'keyword')
                ->orderByRaw('LENGTH(`value`) DESC')
                ->get(['value'])
                ->pluck('value')
                ->toArray();
        });
    }

    /**
     * Get block word length and pinyin.
     *
     * @param string $word
     *
     * @return array
     */
    protected function transformBlockWord($word)
    {
        return [
            'len' => mb_strlen($word),
            'pinyin' => (new Pinyin())->sentence($word),
        ];
    }

    /**
     * Replace block word with ♥ symbol.
     *
     * @param array $word
     * @param string $content
     *
     * @return string
     */
    protected function replaceBlockWord($word, $content)
    {
        $pinyin = new Pinyin();

        if (! str_contains($pinyin->sentence($content), $word['pinyin'])) {
            return $content;
        }

        $contentLen = mb_strlen($content) - $word['len'] + 1;

        for ($i = 0; $i < $contentLen; ++$i) {
            if ($pinyin->sentence(mb_substr($content, $i, $word['len'])) === $word['pinyin']) {
                $content = implode('', [
                    mb_substr($content, 0, $i),
                    str_repeat('♥', $word['len']),
                    mb_substr($content, $i + $word['len']),
                ]);

                $i += ($word['len'] - 1);
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
     * @param bool $all
     *
     * @return null|string
     */
    protected function findLink($content, $all = false)
    {
        $amount = preg_match_all(
            '/\b(https?):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|]/i',
            $content,
            $matches
        );

        if (0 === $amount) {
            return null;
        }

        return $all ? $matches[0] : $matches[0][0];
    }

    /**
     * Create an image using the post content and return the image path.
     *
     * @return string
     */
    protected function canvas()
    {
        $filePath = file_build_path($this->getImageDirectory(), $this->post->getKey().'.jpg');

        $canvas = $this->getCanvasWidthAndHeight($this->post->getAttribute('content'));

        Image::canvas($canvas['width'], $canvas['height'], '#000')
            ->text($this->post->getAttribute('content'), $canvas['width'] / 2, $canvas['height'] / 2, function (Font $font) {
                $font->file($this->getFontPath());
                $font->size(48);
                $font->color('#fff');
                $font->align('center');
                $font->valign('middle');
            })
            ->save($filePath, 100);

        return $filePath;
    }

    /**
     * Get canvas width and height.
     *
     * @param string $content
     *
     * @return array
     */
    protected function getCanvasWidthAndHeight($content)
    {
        $box = imagettfbbox(48, 0, $this->getFontPath(), $content);

        return [
            'width' => abs($box[4] - $box[0]),
            'height' => abs($box[5] - $box[1]),
        ];
    }

    /**
     * Get the text font path.
     *
     * @return string
     */
    protected function getFontPath()
    {
        return storage_path('app/fonts/NotoSansCJKtc-Regular.otf');
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
     * @param string|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return FacebookResponse
     */
    protected function postPhotos($file)
    {
        if (is_string($file)) {
            return $this->fb->post('/me/photos', [
                'source' => new FacebookFile($file),
                'caption' => $this->content(true),
            ]);
        }

        $file = $file->move(
            $this->getImageDirectory(),
            $this->post->getKey().'.'.$file->guessExtension()
        );

        return $this->fb->post('/me/photos', [
            'source' => new FacebookFile($file->getPathname()),
            'caption' => $this->content(),
        ]);
    }

    /**
     * Get the directory that store images.
     *
     * @return string
     */
    protected function getImageDirectory()
    {
        return storage_path(file_build_path('app', 'images', intval(floor($this->post->getKey() / 5000))));
    }

    /**
     * Get post content.
     *
     * @param bool $textImage
     *
     * @return string
     */
    protected function content($textImage = false)
    {
        return implode('', [
            // Page hash tag
            '#'.$this->application['page_name'].$this->post->getKey(),
            $this->newLines(1),

            // Extra content that should insert to the post
            $this->application['extra_content'],
            $this->newLines(2),

            // User post content
            $textImage ? $this->getTextImageLinks() : $this->post->getAttribute('content'),
            $this->newLines(2),

            // Post submitted time
            'Submitted At: '.$this->post->getAttribute('created_at'),
        ]);
    }

    /**
     * Get all links from text image.
     *
     * @return string
     */
    protected function getTextImageLinks()
    {
        $links = $this->findLink($this->post->getAttribute('content'), true);

        if (is_null($links)) {
            return '';
        }

        return implode($this->newLines(), $links);
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
