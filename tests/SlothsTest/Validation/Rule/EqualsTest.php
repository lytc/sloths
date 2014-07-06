<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Equals;

class EqualsTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $comparator, $input)
    {
        $this->assertSame($expected, (new Equals($comparator))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 1, 1],
            [true, 0, 0],
            [true, true, true],
            [true, false, false],
            [true, '', ''],
            [true, 'a', 'a'],
            [true, 1.1, 1.1],
            [true, 1, true],
            [true, 0, false],
            [false, 1, 2],
            [true, '1.1', 1.1]
        ];
    }
}