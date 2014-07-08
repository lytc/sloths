<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Contains;

class ContainsTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $comparator, $input, $strict = false)
    {
        $this->assertSame($expected, (new Contains($comparator, $strict))->validate($input));
    }

    public function dataProvider()
    {
        return [
            // string
            [true, 'abc', 'a'],
            [true, 'abc', 'b'],
            [false, 'abc', 'd'],
            [true, 'aBc', 'b'],
            [false, 'aBc', 'b', true],

            // array
            [true, [1, 2, 3], 2],
            [true, [1, [2], 3], [2]],
            [false, [1, 2, 3], 4],
            [true, [1, 2, 3], '2'],
            [false, [1, 2, 3], '2', true],
        ];
    }

    /**
     * @dataProvider invalidComparatorDataProvider
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidComparatorShouldThrowAnException($comparator)
    {
        new Contains($comparator);
    }

    public function invalidComparatorDataProvider()
    {
        return [
            [true],
            [1],
            [new \stdClass()]
        ];
    }
}