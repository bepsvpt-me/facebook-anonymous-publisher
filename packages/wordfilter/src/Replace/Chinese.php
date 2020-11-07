<?php

namespace FacebookAnonymousPublisher\Wordfilter\Replace;

use Overtrue\Pinyin\Pinyin;

class Chinese implements Replace
{
    /**
     * Pinyin instance.
     *
     * @var Pinyin
     */
    protected $pinyin;

    /**
     * Punctuations map.
     *
     * @var array
     */
    protected $punctuations = [
        '【', '】', '[', ']', '(', ')', '{', '}', '<', '>', '〔', '〕', '⟨', '⟩',
        '–', '—', '―', '＿',
        '《', '》', '〈', '〉',
        '「', '」', '『', '』',
        '：', '，', '、', '…', '！', '。', '～', '？', '；',
    ];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->pinyin = new Pinyin();
    }

    /**
     * Replace words with replacements in a text.
     *
     * @param array $words
     * @param string $replacement
     * @param string $text
     *
     * @return string
     */
    public static function replace(array $words, $replacement, $text)
    {
        $self = new self;

        $length = $self->wordsLength($words);

        $blocks = array_unique($length);

        $min = end($blocks);

        $chars = $self->splitText($text);

        $index = $self->index($chars);

        $pinyins = $self->pinyin->convert($text);

        $len = count($pinyins) - $min + 1;

        for ($i = 0; $i < $len; ++$i) {
            foreach ($blocks as $block) {
                if ($i + $block >= $len + $min) {
                    continue;
                }

                $hash = md5(implode(' ', array_slice($pinyins, $i, $block)));

                if (isset($length[$hash])) {
                    for ($c = 0; $c < $block; ++$c) {
                        $chars[$index[$i + $c]] = $replacement;
                    }

                    $i += $block - 1;

                    break;
                }
            }
        }

        return implode('', $chars);
    }

    /**
     * Get echo word length and use md5 as key.
     *
     * @param array $words
     *
     * @return array
     */
    protected function wordsLength(array $words)
    {
        $length = [];

        foreach ($words as $word) {
            $pinyin = $this->pinyin->sentence($word);

            $length[md5($pinyin)] = ($pinyin === $word)
                ? 1
                : mb_strlen($word);
        }

        arsort($length);

        return $length;
    }

    /**
     * Spilt text to array.
     *
     * @param string $text
     *
     * @return array
     */
    protected function splitText($text)
    {
        $punctuations = preg_quote(implode('', $this->punctuations), '/');

        $text = preg_replace('/([^\p{Han}\s'.$punctuations.']+)/u', "\t\t\t$1\t\t", $text);

        $array = [];

        foreach (preg_split('/\t\t/u', $text, -1, PREG_SPLIT_NO_EMPTY) as $item) {
            $array[] = ("\t" === $item[0])
                ? substr($item, 1)
                : preg_split('//u', $item, -1, PREG_SPLIT_NO_EMPTY);
        }

        return $this->flatten($array);
    }

    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param array $array
     *
     * @return array
     */
    protected function flatten($array)
    {
        return array_reduce($array, function ($result, $item) {
            if (! is_array($item)) {
                return array_merge($result, [$item]);
            }

            return array_merge($result, $this->flatten($item));
        }, []);
    }

    /**
     * Build words index.
     *
     * @param array $chars
     *
     * @return array
     */
    protected function index(array $chars)
    {
        $index = [];

        foreach ($chars as $key => $char) {
            if (preg_match('/[\p{Han}\w]+/u', $char)) {
                $index[] = $key;
            }
        }

        return $index;
    }
}
