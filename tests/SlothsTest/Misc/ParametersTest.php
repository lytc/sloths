<?php

namespace SlothsTest\Misc;

use Sloths\Misc\Parameters;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Misc\Parameters
 */
class ParametersTest extends TestCase
{
    public function test()
    {
        $parameters = new Parameters(['foo' => 'bar']);

        $this->assertCount(1, $parameters);
        $this->assertTrue($parameters->has('foo'));
        $this->assertSame('bar', $parameters->get('foo'));

        $parameters->set('bar', 'baz');
        $this->assertCount(2, $parameters);
        $this->assertTrue($parameters->has('bar'));
        $this->assertSame('baz', $parameters->get('bar'));

        $parameters->remove('foo');
        $this->assertCount(1, $parameters);
        $this->assertFalse($parameters->has('foo'));
        $this->assertNull($parameters->get('foo'));
    }

    public function testToArray()
    {
        $parameters = new Parameters(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $parameters->toArray());
    }

    public function testReset()
    {
        $parameters = new Parameters(['foo' => 'bar', 'bar' => 'baz']);
        $parameters->reset();
        $this->assertSame([], $parameters->toArray());
    }

    public function testTrim()
    {
        $parameters = new Parameters(['foo' => ' bar ', 'bar' => ['bar']]);
        $this->assertSame(['foo' => 'bar', 'bar' => ['bar']], $parameters->trim()->toArray());
    }

    public function testOnly()
    {
        $parameters = new Parameters(['foo' => 'bar', 'bar' => 'baz']);
        $this->assertSame(['bar' => 'baz'], $parameters->only(['bar', 'qux'])->toArray());
        $this->assertSame(['bar' => 'baz'], $parameters->only('bar', 'qux')->toArray());
    }

    public function testExcept()
    {
        $parameters = new Parameters(['foo' => 'bar', 'bar' => 'baz']);
        $this->assertSame(['bar' => 'baz'], $parameters->except('foo', 'qux')->toArray());
    }
}