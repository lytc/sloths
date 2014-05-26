<?php

namespace SlothsTest\View\Helper;

use Sloths\View\View;

/**
 * @covers \Sloths\View\Helper\InputEmailTag<extended>
 */
class InputEmailTagTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $view = new View();
        $expected = '<input type="email" placeholder="Foo" name="foo" value="bar">';
        $this->assertSame($expected, (String) $view->inputEmailTag('foo', 'bar', ['placeholder' => 'Foo']));
    }
}