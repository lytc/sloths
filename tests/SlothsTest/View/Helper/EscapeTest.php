<?php

namespace SlothsTest\View\Helper;

use SlothsTest\TestCase;
use Sloths\View\Helper\Escape;

/**
 * @covers Sloths\View\Helper\Escape
 */
class EscapeTest extends TestCase
{
    public function test()
    {
        $escape = new Escape();
        $this->assertSame('&lt;div&gt;', $escape('<div>'));
    }
}