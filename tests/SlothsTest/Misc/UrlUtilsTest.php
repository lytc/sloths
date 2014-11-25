<?php

namespace SlothsTest\Misc;
use Sloths\Misc\UrlUtils;

/**
 * @covers \Sloths\Misc\UrlUtils
 */
class UrlUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProviderTestAppendParams
     */
    public function testAppendParams($url, $params, $expected)
    {
        $this->assertSame($expected, UrlUtils::appendParams($url, $params));
    }

    public function dataProviderTestAppendParams()
    {
        return [
            ['foo', ['bar' => 'baz'], 'foo?bar=baz'],
            ['foo?', ['bar' => 'baz'], 'foo?bar=baz'],
            ['/foo?foo=bar', ['bar' => 'baz'], '/foo?foo=bar&bar=baz'],
            ['/foo?bar=qux', ['bar' => 'baz'], '/foo?bar=baz'],
            ['http://foo.com/foo?foo=bar', ['bar' => 'baz'], 'http://foo.com/foo?foo=bar&bar=baz'],
            ['foo', [], 'foo']
        ];
    }
}