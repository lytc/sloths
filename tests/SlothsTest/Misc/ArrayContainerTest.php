<?php

namespace SlothsTest\Misc;

use Sloths\Misc\ArrayContainer;
use SlothsTest\TestCase;

class ArrayContainerTest extends TestCase
{
    public function testSetAndGet()
    {
        $hash = new ArrayContainer();
        $this->assertNull($hash->foo);

        $hash->foo = 'foo';
        $this->assertSame('foo', $hash->foo);

        $hash->bar = ['baz' => 'qux'];
        $this->assertInstanceOf('Sloths\Misc\ArrayContainer', $hash->bar);
        $this->assertSame('qux', $hash->bar->baz);
    }

    public function testCount()
    {
        $hash = new ArrayContainer();
        $this->assertCount(0, $hash);

        $hash->foo = 'bar';
        $this->assertCount(1, $hash);
    }

    public function testGetIterator()
    {
        $hash = new ArrayContainer();
        $this->assertInstanceOf('ArrayIterator', $hash->getIterator());
    }

    public function testMerge()
    {
        $hash = new ArrayContainer(['foo' => 'foo']);
        $hash->merge(new ArrayContainer(['foo' => 'bar', 'bar' => 'baz']));

        $this->assertSame(['foo' => 'bar', 'bar' => 'baz'], $hash->toArray());
    }

    public function testNested()
    {
        $hash = new ArrayContainer([
            'foo' => [
                'bar' => 'baz'
            ]
        ], true);

        $this->assertInstanceOf('Sloths\Misc\ArrayContainer', $hash->foo);
        $this->assertSame('baz', $hash->foo->bar);
    }

    public function testReplaceRecursive()
    {
        $hash = new ArrayContainer([
            'foo' => [
                'bar' => 'baz',
                'baz' => 'qux'
            ]
        ], true);

        $hash->replaceRecursive(new ArrayContainer([
            'foo' => [
                'baz' => 'wot'
            ]
        ]));

        $this->assertSame('baz', $hash->foo->bar);
        $this->assertSame('wot', $hash->foo->baz);
    }
}