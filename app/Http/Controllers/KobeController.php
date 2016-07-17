<?php

namespace App\Http\Controllers;

use App\Block;
use App\Config;
use App\Http\Requests\KobeRequest;
use App\Post;
use Cache;
use Carbon\Carbon;
use Crypt;
use Facebook\Facebook;
use Facebook\FacebookResponse;
use Facebook\FileUpload\FacebookFile;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Image;
use Intervention\Image\Gd\Font;
use Mexitek\PHPColors\Color;
use Overtrue\Pinyin\Pinyin;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
     * Post kobe.
     *
     * @param KobeRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function kobe(KobeRequest $request)
    {
        $this->verify($request);

        $this->init();

        $this->save($request);

        $file = $request->has('post-by-image') ? $this->canvas($request->input('color')) : $request->file('image');

        $this->posted($this->postFeed($file));

        return redirect("https://www.facebook.com/{$this->post->getAttribute('fbid')}");
    }

    /**
     * Verify the request is valid or not.
     *
     * @param Request $request
     *
     * @return void
     */
    protected function verify(Request $request)
    {
        if ('kobe.non-secure' === $request->route()->getName()) {
            $legal = true;

            try {
                if ('daily-top' !== Crypt::decrypt($request->input('scheduling-auth', ''))) {
                    $legal = false;
                }
            } catch (DecryptException $e) {
                $legal = false;
            }

            if (! $legal) {
                throw new AccessDeniedHttpException;
            }
        }
    }

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
     * Create post.
     *
     * @param Request $request
     *
     * @return bool
     */
    protected function save(Request $request)
    {
        $content = $this->filterBlockWords(
            $this->stripCharacters(
                $this->normalizeNewLine(
                    $request->input('content')
                )
            )
        );

        $this->post->setAttribute('user_id', (is_block_ip(false) && ! is_null($request->user())) ? $request->user()->getKey() : null);
        $this->post->setAttribute('content', $this->transformHashTag($content));
        $this->post->setAttribute('link', $request->has('nolink') ? null : $this->findLink($content));
        $this->post->setAttribute('has_image', $request->has('post-by-image') || $request->hasFile('image'));
        $this->post->setAttribute('user_agent', $request->header('user-agent'));
        $this->post->setAttribute('ip', real_ip());
        $this->post->setAttribute('created_at', Carbon::now());
        $this->post->setAttribute('sync_at', Carbon::now());

        return $this->post->save();
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
     * Strip special characters.
     *
     * @param string $string
     *
     * @return string
     */
    protected function stripCharacters($string)
    {
        $removes = [];

        foreach ($this->stringToArray($string) as $char) {
            if ((1 === strlen($char)) && ($this->newLines(1) !== $char) && (! ctype_print($char) || ctype_cntrl($char))) {
                $removes[] = $char;
            }
        }

        return str_replace(array_merge(array_unique($removes, SORT_REGULAR), [d('&lrm;')]), '', $string);
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
        $words = $this->blockWords();

        if (empty($words)) {
            return $content;
        }

        if (! preg_match("/\p{Han}+/uU", $content)) {
            return str_replace($words, $this->simpleReplacements($words), $content);
        }

        return $this->advanceFilter($content, $words);
    }

    /**
     * Get block words.
     *
     * @return array
     */
    protected function blockWords()
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
     * Create isometric characters for the block words.
     *
     * @param array $words
     *
     * @return array
     */
    protected function simpleReplacements(array $words)
    {
        $replacements = [];

        foreach ($words as $word) {
            $replacements[] = str_repeat($this->application['block_word_replacement'] ?? '', mb_strlen($word));
        }

        return $replacements;
    }

    /**
     * Advance filter the block words.
     *
     * @param string $content
     * @param array $words
     *
     * @return string
     */
    protected function advanceFilter($content, $words)
    {
        $hashes = $this->wordsHash($words);

        $blocks = array_unique($hashes);
        rsort($blocks);

        $chars = $this->stringToArray($content);
        $indexes = $this->assocIndex($chars);

        $pinyins = (new Pinyin())->convert($content);

        $len = count($pinyins) - $blocks[count($blocks) - 1] + 1;

        for ($i = 0; $i < $len; ++$i) {
            foreach ($blocks as $block) {
                if ($i + $block > $len + 1) {
                    continue;
                }

                $hash = md5(implode(' ', array_slice($pinyins, $i, $block)));

                if (isset($hashes[$hash])) {
                    for ($c = 0; $c < $block; ++$c) {
                        $chars[$indexes[$i + $c]] = $this->application['block_word_replacement'] ?? '';
                    }

                    $i += $block - 1;

                    break;
                }
            }
        }

        return implode('', $chars);
    }

    /**
     * Build words hash for multiple pattern matching.
     *
     * @param array $words
     *
     * @return array
     */
    protected function wordsHash($words)
    {
        $pinyin = new Pinyin();

        $hash = [];

        foreach ($words as $word) {
            $hash[md5($pinyin->sentence($word))] = mb_strlen($word);
        }

        return $hash;
    }

    /**
     * Get the associate index of the chars.
     *
     * @param array $chars
     *
     * @return array
     */
    protected function assocIndex(array $chars)
    {
        $indexes = [];

        foreach ($chars as $index => $char) {
            if (preg_match('/^[\p{Han}a-zA-Z\d]+$/u', $char)) {
                $indexes[] = $index;
            }
        }

        return $indexes;
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
        if (0 === preg_match_all('/#('.$this->application['page_name'].')?(\d+)/', $content, $matches)) {
            return $content;
        }

        $stack = [];

        foreach ($matches[2] as $index => $match) {
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
     * @param string $color
     *
     * @return string
     */
    protected function canvas($color)
    {
        $filePath = file_build_path($this->imageDirectory(), $this->post->getKey().'.jpg');

        $content = $this->breakLine($this->post->getAttribute('content'));

        $canvas = $this->getCanvasWidthAndHeight($content);

        Image::canvas($canvas['width'], $canvas['height'], '#'.$color)
            ->text($content, $canvas['width'] / 2, $canvas['height'] / 2, function (Font $font) use ($color) {
                $font->file($this->getFontPath());
                $font->size(48);
                $font->color((new Color($color))->isDark() ? '#fff' : '#000');
                $font->align('center');
                $font->valign('middle');
            })
            ->save($filePath, 100);

        return $filePath;
    }

    /**
     * Break the line to multiple lines if length is too long.
     *
     * @param string $content
     *
     * @return string
     */
    protected function breakLine($content)
    {
        $lines = explode($this->newLines(1), $content);

        foreach ($lines as &$line) {
            if (mb_strwidth($line) > 48) {
                $line = $this->partitionLine($line);
            }
        }

        return implode($this->newLines(1), $lines);
    }

    /**
     * Ensure the length of the line is not longer than 48.
     *
     * @param string $line
     *
     * @return string
     */
    protected function partitionLine($line)
    {
        list($lines, $temp, $width) = [[], '', 0];

        foreach ($this->stringToArray($line) as $word) {
            if ($width + mb_strwidth($word) > 48) {
                $lines[] = $temp;
                $temp = '';
                $width = 0;
            }

            $temp .= $word;
            $width += mb_strwidth($word);
        }

        return implode($this->newLines(1), $lines);
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
        return storage_path(file_build_path('app', 'fonts', 'NotoSansCJKtc-Regular.otf'));
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
            $this->imageDirectory(),
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
    protected function imageDirectory()
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
            $this->application['extra_content'] ?? '',
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

        return implode($this->newLines(1), $links);
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

    /**
     * Convert string to array.
     *
     * @param string $string
     *
     * @return array
     */
    protected function stringToArray($string)
    {
        $string = preg_replace('/([^\p{Han}\s？，、。！：「」『』；—]+)/u', "\t\t\t$1\t\t", $string);

        $chars = [];

        foreach (preg_split('/\t\t/u', $string, -1, PREG_SPLIT_NO_EMPTY) as $item) {
            $chars[] = ("\t" === $item[0])
                ? substr($item, 1)
                : preg_split('//u', $item, -1, PREG_SPLIT_NO_EMPTY);
        }

        return array_flatten($chars);
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
}
