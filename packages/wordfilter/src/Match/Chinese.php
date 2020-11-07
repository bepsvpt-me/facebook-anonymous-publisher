<?php

namespace FacebookAnonymousPublisher\Wordfilter\Match;

use Overtrue\Pinyin\Pinyin;

class Chinese implements Match
{
    /**
     * Pinyin instance.
     *
     * @var Pinyin
     */
    protected $pinyin;

    /**
     * @var string
     */
    protected $determine;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->pinyin = new Pinyin();

        $this->determine = substr(md5(mt_rand()), 0, 24);
    }

    /**
     * Determines the given text contain any of words.
     *
     * @param string $text
     * @param array $words
     *
     * @return bool
     */
    public static function match($text, array $words)
    {
        $self = new self;

        $text = implode(' ', $self->pinyin->convert($text));

        foreach ($self->transformWords($words) as $word) {
            if (false !== mb_strpos($text, $word)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Transform words to pinyin.
     *
     * @param array $words
     *
     * @return array
     */
    protected function transformWords(array $words)
    {
        $text = implode(" {$this->determine} ", $words);

        $text = $this->pinyin->sentence($text);

        return explode(" {$this->determine} ", $text);
    }
}
