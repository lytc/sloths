<?php

namespace LazyTest\View\Helper;

use Lazy\View\View;

class TextAreaTagTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $view = new View();
        $expected = '<textarea placeholder="Foo" name="foo">bar</textarea>';
        $this->assertSame($expected, (String) $view->textAreaTag('foo', 'bar', ['placeholder' => 'Foo']));
    }
}