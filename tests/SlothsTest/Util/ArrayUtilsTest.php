<?php

namespace SlothsTest\Util;
use Sloths\Util\ArrayUtils;

/**
 * @covers \Sloths\Util\ArrayUtils
 */
class ArrayUtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testPick()
    {
        $values = ['foo' => 1, 'bar' => 2, 'baz' => 3, 'qux' => 4];
        $pickKeys = 'bar qux wot';
        $expected = ['bar' => 2, 'qux' => 4];

        $this->assertSame($expected, ArrayUtils::pick($values, $pickKeys));
    }

    public function testPickWithDefaultValue()
    {
        $values = ['foo' => 1, 'bar' => 2, 'baz' => 3, 'qux' => 4];
        $pickKeys = 'bar qux wot';
        $expected = ['bar' => 2, 'qux' => 4, 'wot' => null];

        $this->assertSame($expected, ArrayUtils::pick($values, $pickKeys, null));
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