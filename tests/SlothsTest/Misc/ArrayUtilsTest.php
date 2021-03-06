<?php

namespace SlothsTest\Misc;
use Sloths\Misc\ArrayUtils;

/**
 * @covers Sloths\Misc\ArrayUtils
 */
class ArrayUtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testMethodOnly()
    {
        $values = ['foo' => 1, 'bar' => 2, 'baz' => 3, 'qux' => 4];
        $keys = 'bar qux wot';
        $expected = ['bar' => 2, 'qux' => 4];

        $this->assertSame($expected, ArrayUtils::only($values, $keys));
    }

    public function testMethodOnlyWithDefaultValue()
    {
        $values = ['foo' => 1, 'bar' => 2, 'baz' => 3, 'qux' => 4];
        $keys = 'bar qux wot';
        $expected = ['bar' => 2, 'qux' => 4, 'wot' => null];

        $this->assertSame($expected, ArrayUtils::only($values, $keys, null));
    }

    public function testMethodExcept()
    {
        $values = ['foo' => 1, 'bar' => 2, 'baz' => 3];

        $this->assertSame(['foo' => 1], ArrayUtils::except($values, 'bar baz'));
        $this->assertSame(['foo' => 1], ArrayUtils::except($values, ['bar', 'baz']));
    }

    public function testHasOnlyInts()
    {
        $this->assertTrue(ArrayUtils::hasOnlyInts([1]));
        $this->assertTrue(ArrayUtils::hasOnlyInts(['1']));
        $this->assertFalse(ArrayUtils::hasOnlyInts([1.1]));
        $this->assertFalse(ArrayUtils::hasOnlyInts(['1.1']));
        $this->assertFalse(ArrayUtils::hasOnlyInts([1, '']));
    }

    public function testMethodColumnWithColumnKeyNull()
    {
        $this->assertSame(['foo' => 'bar'], ArrayUtils::column(['foo' => 'bar'], null));

        $result = ArrayUtils::column([
            ['name' => 'foo'],
            ['foo' => 'bar'],
        ], null, 'name');

        $expected = [
            'foo' => ['name' => 'foo'],
            ['foo' => 'bar'],
        ];

        $this->assertSame($expected, $result);
    }

    public function testMethodColumnWithColumnKeyNotNull()
    {
        $this->assertSame(['foo' => 'bar'], ArrayUtils::column(['foo' => 'bar'], null));

        $result = ArrayUtils::column([
            ['name' => 'foo'],
            ['id' => 10, 'name' => 'bar'],
            ['foo' => 'baz'],
        ], 'name', 'id');

        $expected = ['foo', 10 => 'bar'];

        $this->assertSame($expected, $result);
    }
}