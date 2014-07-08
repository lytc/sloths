<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Divisible;

class DivisibleTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input, $comparator)
    {
        $this->assertSame($expected, (new Divisible($comparator))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 2, 2],
            [true, 2, 1],
            [true, '2', '1'],
            [true, '2', '1.0'],
            [true, 0, 1],
            [true, 2.2, 1.1],
            [true, '2.2', '1.1'],
            [false, 3, 2],
            [false, 3.1, 2.1],
            [true, 0, 2.1],
            [true, '0', 2.1],
            [false, 2, 2.1],
            [false, 2.1, 2],
            [false, true, 1],
            [false, false, 1],
            [false, null, 1],
            [false, [], 1],
            [false, new \stdClass(), 1],
        ];
    }

    /**
     * @dataProvider invalidComparatorDataProvider
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidComparatorShouldThrowAnException($input)
    {
        new Divisible($input);
    }

    public function invalidComparatorDataProvider()
    {
        return [
            [''],
            [0],
            ['foo'],
            [null],
            [true],
            [false],
            [[]],
            [new \stdClass()]
        ];
    }
}