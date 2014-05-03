<?php

namespace LazyTest\View\Helper;

use Lazy\View\View;

class InputPasswordTagTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $view = new View();
        $expected = '<input type="password" placeholder="Foo" name="foo" value="bar">';
        $this->assertSame($expected, (String) $view->inputPasswordTag('foo', 'bar', ['placeholder' => 'Foo']));
    }
}