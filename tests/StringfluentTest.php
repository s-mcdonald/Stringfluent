<?php

declare(strict_types=1);

namespace Tests\SamMcDonald\Stringfluent;

use SamMcDonald\Stringfluent\Stringfluent;
use PHPUnit\Framework\TestCase;

class StringfluentTest extends TestCase
{
    public function testCreate(): void
    {
        $this->assertEquals(
            "foo",
            Stringfluent::create('foo')
        );
    }

    public function testEmpty(): void
    {
        $this->assertEquals(
            "",
            Stringfluent::empty()
        );
    }

    public function testTrim(): void
    {
        $this->assertEquals(
            "foo",
            Stringfluent::create("  foo ")->trim()
        );
    }

    /**
     * @depends testCreate
     */
    public function testToUpperCase(): void
    {
        $this->assertEquals(
            Stringfluent::create("FOO BAR BAZ"),
            Stringfluent::create("foo bAr Baz")->toUpperCase()
        );
    }

    public function testToLowerCase(): void
    {
        $this->assertEquals(
            Stringfluent::create("foo bar baz"),
            Stringfluent::create("foo bAr Baz")->toLowerCase()
        );
    }

    public function testToUcFirst(): void
    {
        $this->assertEquals(
            Stringfluent::create("Foo bAr Baz"),
            Stringfluent::create("foo bAr Baz")->toUcFirst()
        );
    }

    public function testToTitleCase(): void
    {
        $this->assertEquals(
            Stringfluent::create("Foo Bar Baz"),
            Stringfluent::create("foo bar Baz")->toTitleCase()
        );
    }

    /**
     * @dataProvider provideDataForTestPrepend
     */
    public function testPrepend(string $expectedResult, string ...$valueToPrepend): void
    {
        $sut = Stringfluent::create('foo');
        $this->assertEquals(
            $expectedResult,
            $sut->prepend(...$valueToPrepend)
        );
    }

    public static function provideDataForTestPrepend(): array
    {
        return [
            'string' => [
                'crikeyfoo',
                'crikey',
            ],
            'multiple words' => [
                'crikeycrazyfoo',
                'crikey',
                'crazy',
            ],
        ];
    }

    /**
     * @dataProvider provideDataForTestAppend
     */
    public function testAppend($valueToAppend, string $expectedResult): void
    {
        $sut = Stringfluent::create('foo');
        $this->assertEquals(
            $expectedResult,
            $sut->append($valueToAppend)
        );
    }

    public static function provideDataForTestAppend(): array
    {
        return [
          'string' => [
              'bar',
              'foobar'
            ],
        ];
    }

    public function testEncase(): void
    {
        $this->assertEquals(
            Stringfluent::create("BazFooBaz"),
            Stringfluent::create("Foo")->encase("Baz")
        );
    }

    public function testTruncate(): void
    {
        $this->assertEquals(
            Stringfluent::create("foo"),
            Stringfluent::create("foo-bar-baz")->truncate(3)
        );
    }

    public function testSubstring(): void
    {
        $this->assertEquals(
            Stringfluent::create("bar-baz"),
            Stringfluent::create("foo-bar-baz")->substring(4)
        );
    }

    public function testPadLeft(): void
    {
        $this->assertEquals(
            Stringfluent::create("XXXXXXXfoo"),
            Stringfluent::create("foo")->padLeft('X', 10)
        );
    }

    public function testPadRight(): void
    {
        $this->assertEquals(
            Stringfluent::create("fooXXXXXXX"),
            Stringfluent::create("foo")->padRight('X', 10)
        );
    }

    public function testReverse(): void
    {
        $this->assertEquals(
            Stringfluent::create("fubar"),
            Stringfluent::create("rabuf")->reverse()
        );
    }

    public function testRepeat(): void
    {
        $this->assertEquals(
            Stringfluent::create("fubarfubar"),
            Stringfluent::create("fubar")->repeat(2)
        );
    }

    /**
     * @dataProvider provideDataForTestStripHtmlTags
     */
    public function testStripHtmlTags($allowedTags, string $expectedResult): void
    {
        $sut = Stringfluent::create('foo<br/>bar<h1>baz</h1>');
        $this->assertEquals(
            $expectedResult,
            $sut->stripHtmlTags($allowedTags)->toString()
        );
    }

    public static function provideDataForTestStripHtmlTags(): array
    {
        return [
            'string' => [
                '<h1>',
                'foobar<h1>baz</h1>'
            ],
        ];
    }

    /**
     * @dataProvider provideDataForTestEncodeHtml
     */
    public function testEncodeHtml(int $flags, string $expectedResult): void
    {
        $sut = Stringfluent::create('foo<br/>bar<h1>baz</h1>');
        $this->assertEquals(
            $expectedResult,
            $sut->encodeHtml($flags)->toString()
        );
    }

    public static function provideDataForTestEncodeHtml(): array
    {
        return [
            [
                ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401,
                'foo&lt;br/&gt;bar&lt;h1&gt;baz&lt;/h1&gt;'
            ],
        ];
    }

    /**
     * @dataProvider provideDataForTestDecodeHtml
     */
    public function testDecodeHtml(int $flags, string $expectedResult): void
    {
        $sut = Stringfluent::create('foo<br/>bar<h1>baz</h1>');
        $this->assertEquals(
            $expectedResult,
            $sut->encodeHtml($flags)->decodeHtml($flags)->toString()
        );
    }

    public static function provideDataForTestDecodeHtml(): array
    {
        return [
            [
                ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401,
                'foo<br/>bar<h1>baz</h1>'
            ],
        ];
    }

    /**
     * @dataProvider provideDataForTestWordCount
     */
    public function testWordCount(string $words, int $expectedResult): void
    {
        $sut = Stringfluent::create($words);
        $this->assertEquals(
            $expectedResult,
            $sut->wordCount()
        );
    }

    public static function provideDataForTestWordCount(): array
    {
        return [
            [
                "the big brown fox said foo",
                6
            ],
        ];
    }

    public function testShuffleCharacters(): void
    {
        $this->assertNotEquals(
            Stringfluent::create("baz")->toString(),
            Stringfluent::create("baz")->shuffleCharacters()
        );
    }

    public function testCharAt(): void
    {
        $this->assertEquals(
            'a',
            Stringfluent::create("baz")->charAt(1)
        );
    }

    public function testEqualTo(): void
    {
        $this->assertTrue(
            Stringfluent::create("foo")->equalTo('foo')
        );
    }

    public function testStartsWith(): void
    {
        $this->assertTrue(
            Stringfluent::create("foo")->startsWith('f')
        );
    }

    public function testEndsWith(): void
    {
        $this->assertTrue(
            Stringfluent::create("baz")->endsWith('z')
        );
    }

    public function testReadmeExample(): void
    {
        $str = Stringfluent::create("  any form of string, or stringable interface can be used. ");

        $this->assertEquals(
            'ANY',
            $str->toUpperCase()->trim()->truncate(3)->toString()
        );
    }

    public function testMultiByteSupport(): void
    {
        $str = Stringfluent::create("プログラミングはクールです");

        $this->assertEquals(
            'プログラミングはクールです',
            $str->toString()
        );

        $this->assertTrue(
            $str->containsMultibyteCharacters()
        );
    }
}
