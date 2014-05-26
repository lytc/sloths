<?php

namespace SlothsTest\Misc;

use Sloths\Misc\ArrayContainer;
use SlothsTest\TestCase;

/**
 * @covers \Sloths\Misc\ArrayContainer
 */
class ArrayContainerTest extends TestCase
{
    public function testSetAndGet()
    {
        $arrayContainer = new ArrayContainer();
        $this->assertNull($arrayContainer->foo);

        $arrayContainer->foo = 'foo';
        $this->assertSame('foo', $arrayContainer->foo);

        $arrayContainer->bar = ['baz' => 'qux'];
        $this->assertInstanceOf('Sloths\Misc\ArrayContainer', $arrayContainer->bar);
        $this->assertSame('qux', $arrayContainer->bar->baz);
    }

    public function testCount()
    {
        $arrayContainer = new ArrayContainer();
        $this->assertCount(0, $arrayContainer);

        $arrayContainer->foo = 'bar';
        $this->assertCount(1, $arrayContainer);
    }

    public function testGetIterator()
    {
        $arrayContainer = new ArrayContainer();
        $this->assertInstanceOf('ArrayIterator', $arrayContainer->getIterator());
    }

    public function testMerge()
    {
        $arrayContainer = new ArrayContainer(['foo' => 'foo']);
        $arrayContainer->merge(new ArrayContainer(['foo' => 'bar', 'bar' => 'baz']));

        $this->assertSame(['foo' => 'bar', 'bar' => 'baz'], $arrayContainer->toArray());
    }

    public function testNested()
    {
        $arrayContainer = new ArrayContainer([
            'foo' => [
                'bar' => 'baz'
            ]
        ], true);

        $this->assertInstanceOf('Sloths\Misc\ArrayContainer', $arrayContainer->foo);
        $this->assertSame('baz', $arrayContainer->foo->bar);
    }

    public function testReplaceRecursive()
    {
        $arrayContainer = new ArrayContainer([
            'foo' => [
                'bar' => 'baz',
                'baz' => 'qux'
            ]
        ], true);

        $arrayContainer->replaceRecursive(new ArrayContainer([
            'foo' => [
                'baz' => 'wot'
            ]
        ]));

        $this->assertSame('baz', $arrayContainer->foo->bar);
        $this->assertSame('wot', $arrayContainer->foo->baz);
    }
    
    public function testJsonSerialize()
    {
        $data = ['foo' => 'bar'];
        $arrayContainer = new ArrayContainer($data);

        $this->assertSame(json_encode($data), json_encode($arrayContainer));
    }
}