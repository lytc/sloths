<?php

namespace LazyTest\View;
use Lazy\View\View;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    public function testUrl()
    {
        $view = new View();
        $this->assertSame('/foo/bar?foo=bar', $view->url('/foo/bar', ['foo' => 'bar']));
    }
}