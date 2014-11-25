<?php

namespace SlothsTest\Translation;

use Sloths\Translation\Translator;
use SlothsTest\TestCase;

/**
 * @cover Sloths\Translation\Translator
 */
class TranslatorTest extends TestCase
{
    public function testDefaultOption()
    {
        $translator = new Translator();
        $translator->setDirectory(__DIR__ . '/fixtures/languages');

        $this->assertSame('message foo', $translator->translate('foo'));
        $this->assertSame('message :name', $translator->translate('bar'));
        $this->assertSame('message bar', $translator->translate('bar', ['name' => 'bar']));

        $this->assertSame('baz', $translator->translate('baz'));
    }

    public function testDefaultFallbackLocale()
    {
        $translator = new Translator();
        $translator->setDirectory(__DIR__ . '/fixtures/languages');
        $translator->setLocale('vi');

        $this->assertSame('message foo', $translator->translate('foo'));
        $this->assertSame('vi message', $translator->translate('bar'));
    }

    public function testFallbackLocale()
    {
        $translator = new Translator();
        $translator->setDirectory(__DIR__ . '/fixtures/languages');
        $this->assertSame('baz', $translator->translate('baz'));

        $translator->setFallbackLocale('vi');
        $this->assertSame('message baz', $translator->translate('baz'));
    }

    public function testThroughTextDomain()
    {
        $translator = new Translator();
        $translator->setDirectory(__DIR__ . '/fixtures/languages');

        $this->assertSame('message qux', $translator->validator->translate('qux'));

        $this->assertSame($translator->validator, $translator->validator);
    }

    /**
     * @dataProvider dataProviderPlural
     */
    public function testPlural($expected, $key, $params = null, $number)
    {
        $translator = new Translator();
        $translator->setDirectory(__DIR__ . '/fixtures/languages');

        $this->assertSame($expected, $translator->translate($key, $params, $number));
    }

    public function dataProviderPlural()
    {
        return [
            ['foo :name', 'plural1', null, 1],
            ['bar :name', 'plural1', null, 2],
            ['There are no apples', 'plural2', null, 0],
            ['There is one apple', 'plural2', null, 1],
            ['There are :count apples', 'plural2', null, 2],
            ['There are :count apples', 'plural2', null, 4],
            ['There are :count apples', 'plural2', null, 19],
            ['There are many apples', 'plural2', null, 20],
            ['There are many apples', 'plural2', null, 100],

            ['foo bar', 'plural1', ['name' => 'bar'], 1],
            ['There are 4 apples', 'plural2', ['count' => 4], 4],
            ['There are no apples', 'plural2', null, null],
            ['There is one apple', 'plural2', 1, null]
        ];
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testMissingMessageFileShouldThrowAnException()
    {
        $translator = new Translator();
        $translator->setDirectory(__DIR__ . '/foo');
        $translator->translate('foo');
    }

    /**
     * @expectedException \LogicException
     */
    public function testInvalidMessageFileFormatShouldThrowAnException()
    {
        $translator = new Translator();
        $translator->setDirectory(__DIR__ . '/fixtures/languages');
        $translator->setTextDomain('invalidformat');

        $translator->translate('foo');
    }
}