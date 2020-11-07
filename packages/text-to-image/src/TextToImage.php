<?php

namespace FacebookAnonymousPublisher\TextToImage;

use Intervention\Image\AbstractFont;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Mexitek\PHPColors\Color;

class TextToImage
{
    /**
     * Canvas background color.
     *
     * @var string
     */
    protected $color;

    /**
     * Canvas font file path.
     *
     * @var string
     */
    protected $fontPath;

    /**
     * Constructor.
     *
     * @param null|string $fontPath
     * @param string $color
     */
    public function __construct($fontPath = null, $color = '000000')
    {
        $this->image = new ImageManager;

        $this->fontPath = $fontPath ?: __DIR__.'/fonts/NotoSansCJKtc-Regular.otf';

        $this->color = $color;
    }

    /**
     * Create a text image.
     *
     * @param string $text
     * @param bool $multiple
     *
     * @return Image|Image[]
     */
    public function make($text, $multiple = false)
    {
        $texts = explode(PHP_EOL, $this->breakText($text));

        $texts = $multiple ? array_chunk($texts, 20) : [$texts];

        $images = [];

        foreach ($texts as $text) {
            $text = trim(implode(PHP_EOL, $text));

            $dimensions = $this->getDimensions($text);

            $images[] = $this->image
                ->canvas($dimensions['width'], $dimensions['height'], "#{$this->color}")
                ->text($text, $dimensions['width'] / 2, $dimensions['height'] / 2, function (AbstractFont $font) {
                    $font->file($this->fontPath)
                        ->size(48)
                        ->color((new Color($this->color))->isDark() ? '#fff' : '#000')
                        ->align('center')
                        ->valign('middle');
                });
        }

        return $multiple ? $images : $images[0];
    }

    /**
     * Calculate canvas width and height.
     *
     * @param string $text
     *
     * @return array
     */
    protected function getDimensions($text)
    {
        $box = imagettfbbox(38, 0, $this->fontPath, $text);

        return [
            'width' => abs($box[4] - $box[0]),
            'height' => abs($box[5] - $box[1]),
        ];
    }

    /**
     * Ensure the length of each line is not longer than 48.
     *
     * @param string $text
     *
     * @return string
     */
    public function breakText($text)
    {
        $lines = explode(PHP_EOL, $text);

        foreach ($lines as &$line) {
            if (mb_strwidth($line) > 48) {
                $line = $this->segmentLine($line);
            }
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * Segment text to multiple lines if needed.
     *
     * @param string $text
     *
     * @return string
     */
    protected function segmentLine($text)
    {
        list($lines, $temp, $width) = [[], '', 0];

        foreach (preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY) as $word) {
            if ($width + mb_strwidth($word) > 48) {
                $lines[] = $temp;
                $temp = '';
                $width = 0;
            }

            $temp .= $word;
            $width += mb_strwidth($word);
        }

        $lines[] = $temp;

        return implode(PHP_EOL, $lines);
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set canvas background color.
     *
     * @param string $color
     *
     * @return $this
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFontPath()
    {
        return $this->fontPath;
    }

    /**
     * Set canvas font file path.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setFont($path)
    {
        $this->fontPath = $path;

        return $this;
    }
}
