<?php

namespace SlothsTest\View\Helper;

use Sloths\View\View;

/**
 * @covers \Sloths\View\Helper\InputPasswordTag<extended>
 */
class InputPasswordTagTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $view = new View();
        $expected = '<input type="password" placeholder="Foo" name="foo" value="bar">';
        $this->assertSame($expected, (String) $view->inputPasswordTag('foo', 'bar', ['placeholder' => 'Foo']));
    }
}