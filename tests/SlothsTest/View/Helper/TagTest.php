<?php

namespace SlothsTest\View\Helper;

use Sloths\View\View;

class TagTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $view = new View();
        $expected = '<div></div>';

        $this->assertSame($expected, (String) $view->tag('div'));
    }

    public function testWithAttribute()
    {
        $view = new View();
        $expected = '<link href="/foo" rel="stylesheet">';

        $this->assertSame($expected, (String) $view->tag('link', ['href' => '/foo', 'rel' => 'stylesheet']));
    }
}