<?php

namespace SlothsTest\Misc;

use SlothsTest\TestCase;
use Sloths\Misc\DynamicPropertyTrait;


/**
 * @covers Sloths\Misc\DynamicPropertyTrait
 */
class DynamicPropertyTraitTest extends TestCase
{
    public function test()
    {
        $obj = new DynamicProperty();
        $obj->addDynamicProperty('foo', 'bar');
        $obj->addDynamicProperties(['bar' => 'baz']);

        $this->assertSame('bar', $obj->foo);
        $this->assertSame('baz', $obj->bar);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testGetUndefinedPropertyShouldThrowAnException()
    {
        $obj = new DynamicProperty();
        $obj->foo;
    }
}

class DynamicProperty
{
    use DynamicPropertyTrait;
}