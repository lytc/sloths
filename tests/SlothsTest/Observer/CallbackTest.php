<?php

namespace SlothsTest\Observer;

use Sloths\Observer\Callback;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Observer\Callback
 */
class CallbackTest extends TestCase
{
    public function testCall()
    {
        $callback = new Callback(function($foo) {
            return $foo;
        }, 1);

        $this->assertSame('foo', $callback->call(['foo']));
        $this->assertFalse($callback->call(['foo']));
    }
}