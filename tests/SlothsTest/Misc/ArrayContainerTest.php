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

    public function testMethodMap()
    {
        $data = ['foo' => 1, 'bar' => 2];
        $arrayContainer = new ArrayContainer($data);

        $newArrayContainer = $arrayContainer->map(function($v) {
            return $v + 1;
        });

        $this->assertSame(['foo' => 2, 'bar' => 3], $newArrayContainer->toArray());
    }

    public function testMethodTrim()
    {
        $data = ['foo' => ' foo  '];
        $arrayContainer = new ArrayContainer($data);
        $trimmedArrayContainer = $arrayContainer->trim();

        $this->assertSame(['foo' => 'foo'], $trimmedArrayContainer->toArray());
    }

    public function testMethodOnly()
    {
        $data = ['foo' => 1, 'bar' => 2, 'baz' => 3];
        $arrayContainer = new ArrayContainer($data);

        $this->assertSame(['foo' => 1, 'baz' => 3], $arrayContainer->only('foo baz')->toArray());
        $this->assertSame(['foo' => 1, 'baz' => 3], $arrayContainer->only(['foo', 'baz'])->toArray());
    }

    public function testMethodExcept()
    {
        $data = ['foo' => 1, 'bar' => 2, 'baz' => 3];
        $arrayContainer = new ArrayContainer($data);

        $this->assertSame(['bar' => 2], $arrayContainer->except('foo baz')->toArray());
        $this->assertSame(['bar' => 2], $arrayContainer->except(['foo', 'baz'])->toArray());
    }
}