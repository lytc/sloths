<?php

namespace LazyTest\View;
use Lazy\Http\Request;
use Lazy\View\Helper\Url;
use Lazy\View\View;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    public function testUrl()
    {
        $view = new View();
        $request = new Request();
        $request->pathInfo('/foo/bar');

        Url::setRequest($request);
        $this->assertSame('/foo/bar?foo=bar', $view->url(['foo' => 'bar']));
    }
}