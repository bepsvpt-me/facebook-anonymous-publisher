<?php

use FacebookAnonymousPublisher\Wordfilter\Wordfilter;

class WordfilterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Wordfilter
     */
    protected $wordfilter;

    public function setUp()
    {
        parent::setUp();

        $this->wordfilter = new Wordfilter();
    }

    public function test_match()
    {
        $text = 'I have a pen, i have an apple.';

        $this->assertTrue($this->wordfilter->match($text, ['apple']));
        $this->assertTrue($this->wordfilter->match($text, ['bbb', 'ccc', 'an']));
        $this->assertFalse($this->wordfilter->match($text, ['bbb', 'ccc']));
        $this->assertFalse($this->wordfilter->match($text, ['你好嗎', '我很好', '哈囉']));

        $text = '我們這一家，今天天氣真好';

        $this->assertTrue($this->wordfilter->match($text, ['天氣', '我很好', '我們']));
        $this->assertTrue($this->wordfilter->match($text, ['我很好', '你們', '天起']));
        $this->assertFalse($this->wordfilter->match($text, ['你好嗎', '我很好', '哈囉']));
        $this->assertFalse($this->wordfilter->match($text, ['apple', 'banana', 'car']));

        $text = '我們apple這一banana 家，今 car天天氣真好dog';

        $this->assertTrue($this->wordfilter->match($text, ['你好嗎', '我很好', '哈囉', 'car']));
        $this->assertTrue($this->wordfilter->match($text, ['www', '家今']));
        $this->assertTrue($this->wordfilter->match($text, ['好', '我很好', '我們']));
        $this->assertFalse($this->wordfilter->match($text, ['www', 'aaa', '你們們']));

        $text = '你好嗎';

        $this->assertTrue($this->wordfilter->match($text, ['你']));
        $this->assertTrue($this->wordfilter->match($text, ['嗎']));
        $this->assertFalse($this->wordfilter->match($text, ['我們']));
    }

    public function test_match_exact()
    {
        $text = '我們這一家 Car，今天天氣真好';

        $this->assertTrue($this->wordfilter->matchExact($text, ['天氣', '我很好', '我們']));
        $this->assertFalse($this->wordfilter->matchExact($text, ['你好嗎', '我很好', '哈囉']));
        $this->assertFalse($this->wordfilter->matchExact($text, ['apple', 'car']));
    }

    public function test_replace()
    {
        $text = 'I have a pen, i have an apple.';

        $this->assertSame('I have a pen, i have an apple.', $this->wordfilter->replace([], '', $text));
        $this->assertSame('I have a pen, i have an apple.', $this->wordfilter->replace(['天氣', '我很好', '我們'], '', $text));
        $this->assertSame('I have a pen, i have an .', $this->wordfilter->replace(['apple', 'banana'], '', $text));

        $text = '22我們這一家apple，今天天氣真好! oh ya ~';

        $this->assertSame('22這一家apple，今天真好! oh ya ~', $this->wordfilter->replace(['天氣', '我很好', '我們'], '', $text));
        $this->assertSame('22這一家apple，今天真好! oh ya ~', $this->wordfilter->replace(['添氣', '我很好', '我們'], '', $text));
        $this->assertSame('22我們這一家，今天天氣真好! oh  ~', $this->wordfilter->replace(['apple', 'ya'], '', $text));

        $text = '22我們car這一apple家，今天天氣真好!';

        $this->assertSame('22我們car這一apple家，今天6666666666真66666!', $this->wordfilter->replace(['天氣', '我很好', '好'], '66666', $text));
        $this->assertSame('22我們car這一@家，今天@@真好!', $this->wordfilter->replace(['添氣', 'apple'], '@', $text));
        $this->assertSame('22們car這一apple，天天氣真好!', $this->wordfilter->replace(['家今', 'oh', '我'], '', $text));

        $text = '你好嗎';

        $this->assertSame('好嗎', $this->wordfilter->replace(['你', '最長的句子'], '', $text));
        $this->assertSame('你好', $this->wordfilter->replace(['嗎', '最長的句子'], '', $text));
        $this->assertSame('你', $this->wordfilter->replace(['好嗎', '我', '最長的句子'], '', $text));
        $this->assertSame('嗎', $this->wordfilter->replace(['你好', '我', '最長的句子'], '', $text));
        $this->assertSame('', $this->wordfilter->replace(['你好嗎', '我', '最長的句子'], '', $text));
    }

    public function test_replace_exact()
    {
        $text = '我們這一家，今天天氣真好';

        $this->assertSame('我們這一家，今天天氣真好', $this->wordfilter->replaceExact(['你好嗎', '我很好', '哈囉'], '', $text));
        $this->assertSame('嗨嗨嗨這一家，今天嗨嗨嗨真好', $this->wordfilter->replaceExact(['天氣', '我很好', '我們'], '嗨嗨嗨', $text));
        $this->assertSame('ww這一家，今天ww真好', $this->wordfilter->replaceExact(['天氣', '我很好', '我們'], 'w', $text));
        $this->assertSame('這一家，今天真好', $this->wordfilter->replaceExact(['天氣', '我很好', '我們'], '', $text));
    }
}
