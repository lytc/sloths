<?php

namespace LazyTest\View\Helper;

use Lazy\Http\Request;
use Lazy\View\Helper\Url;
use Lazy\View\View;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    public function testWithDefaultUrl()
    {
        Url::setDefaultUrl('/foo');
        $view = new View();

        $this->assertSame('/foo?bar=baz', $view->url(['bar' => 'baz']));
    }

    public function testDefaultUrlWithClosure()
    {
        $request = new Request(['_SERVER' => ['REQUEST_URI' => '/foo?foo=bar']]);

        Url::setDefaultUrl(function() use ($request) {
            return $request->getUrl();
        });

        $view = new View();
        $this->assertSame('/foo?foo=bar&bar=baz', $view->url(['bar' => 'baz']));
    }

    public function test()
    {
        $view = new View();
        $this->assertSame('/foo?bar=baz', $view->url('/foo', ['bar' => 'baz']));
        $this->assertSame('/foo?foo=bar&bar=baz', $view->url('/foo?foo=bar', ['bar' => 'baz']));
        $this->assertSame('/foo?foo=bar&bar=baz', $view->url('/foo?foo=qux', ['foo' => 'bar', 'bar' => 'baz']));
    }
}