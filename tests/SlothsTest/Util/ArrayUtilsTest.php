<?php

namespace SlothsTest\Util;
use Sloths\Util\ArrayUtils;

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
}