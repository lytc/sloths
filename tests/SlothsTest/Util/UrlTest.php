<?php

namespace SlothsTest\Util;
use Sloths\Util\UrlUtils;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    public function testAppendParams()
    {
        $url = 'foo';
        $this->assertSame('foo?bar=baz', UrlUtils::appendParams($url, ['bar' => 'baz']));

        $url = '/foo?';
        $this->assertSame('/foo?bar=baz', UrlUtils::appendParams($url, ['bar' => 'baz']));

        $url = '/foo?foo=bar';
        $this->assertSame('/foo?foo=bar&bar=baz', UrlUtils::appendParams($url, ['bar' => 'baz']));

        $url = '/foo?bar=qux';
        $this->assertSame('/foo?bar=baz', UrlUtils::appendParams($url, ['bar' => 'baz']));

        $url = 'http://foo.com/foo?foo=bar';
        $this->assertSame('http://foo.com/foo?foo=bar&bar=baz', UrlUtils::appendParams($url, ['bar' => 'baz']));
    }
}