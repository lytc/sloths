<?php

namespace LazyTest\View;
use Lazy\View\View;

class EscapeTest extends \PHPUnit_Framework_TestCase
{
    public function testEscape()
    {
        $view = new View();
        $this->assertSame('&lt;foo&gt;', $view->escape('<foo>'));
    }
}