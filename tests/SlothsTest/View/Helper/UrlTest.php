<?php

namespace SlothsTest\View\Helper;

use Sloths\Http\Request;
use Sloths\View\Helper\Url;
use Sloths\View\View;

/**
 * @covers \Sloths\View\Helper\Url
 */
class UrlTest extends \PHPUnit_Framework_TestCase
{
    public function testWithDefaultUrl()
    {
        Url::setDefaultUrl('/foo');
        $view = new View();

        $this->assertSame('/foo', $view->url());
        $this->assertSame('/foo?bar=baz', $view->url(['bar' => 'baz']));
    }

    public function testDefaultUrlWithClosure()
    {
        $request = new Request();
        $request->setServerVars(['HTTP_HOST' => 'example.com', 'SERVER_PORT' => '80', 'REQUEST_URI' => '/foo?foo=bar']);

        Url::setDefaultUrl(function() use ($request) {
            return $request->getUrl();
        });

        $view = new View();
        $this->assertSame('http://example.com/foo?foo=bar&bar=baz', $view->url(['bar' => 'baz']));
    }

    public function test()
    {
        $view = new View();
        $this->assertSame('/foo?bar=baz', $view->url('/foo', ['bar' => 'baz']));
        $this->assertSame('/foo?foo=bar&bar=baz', $view->url('/foo?foo=bar', ['bar' => 'baz']));
        $this->assertSame('/foo?foo=bar&bar=baz', $view->url('/foo?foo=qux', ['foo' => 'bar', 'bar' => 'baz']));
    }
}