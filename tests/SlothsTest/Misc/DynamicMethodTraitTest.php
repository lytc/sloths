<?php

namespace SlothsTest\Misc;

use SlothsTest\TestCase;
use Sloths\Misc\DynamicMethodTrait;


/**
 * @covers Sloths\Misc\DynamicMethodTrait
 */
class DynamicMethodTraitTest extends TestCase
{
    public function test()
    {
        $obj = new DynamicMethod();
        $obj->addDynamicMethod('foo', function() {return 'bar'; });
        $obj->addDynamicMethods(['bar' => function() {return 'baz';}]);

        $this->assertSame('bar', $obj->foo());
        $this->assertSame('baz', $obj->bar());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testCallUndefinedMethodShouldThrowAnException()
    {
        $obj = new DynamicMethod();
        $obj->foo();
    }
}

class DynamicMethod
{
    use DynamicMethodTrait;
}