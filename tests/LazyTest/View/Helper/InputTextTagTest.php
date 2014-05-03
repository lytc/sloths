<?php

namespace LazyTest\View\Helper;

use Lazy\View\View;

class InputTextTagTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $view = new View();
        $expected = '<input type="text" placeholder="Foo" name="foo" value="bar">';
        $this->assertSame($expected, (String) $view->inputTextTag('foo', 'bar', ['placeholder' => 'Foo']));
    }
}