<?php

namespace LazyTest\View\Helper;

use Lazy\View\View;

class InputEmailTagTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $view = new View();
        $expected = '<input type="email" placeholder="Foo" name="foo" value="bar">';
        $this->assertSame($expected, (String) $view->inputEmailTag('foo', 'bar', ['placeholder' => 'Foo']));
    }
}