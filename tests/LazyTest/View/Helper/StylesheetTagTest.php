<?php

namespace LazyTest\View;
use Lazy\View\View;

class StylesheetTagTest extends \PHPUnit_Framework_TestCase
{
    public function testStylesheetTag()
    {
        $view = new View();
        $this->assertSame('<link rel="stylesheet" href="foo.css">', $view->stylesheetTag('foo.css'));
        $this->assertSame('<link rel="stylesheet" href="foo.css">', $view->stylesheetTag('foo'));
    }
}