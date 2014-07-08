<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Max;

class MaxTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $max,  $input, $inclusive = false)
    {
        $this->assertSame($expected, (new Max($max, $inclusive))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 2, 1],
            [true, -1, -2],
            [true, 2.1, 1.1],
            [true, -1.1, -2.1],
            [true, '2', '1'],
            [true, '-1', '-2'],
            [true, '-1.1', '-2.1'],
            [false, 1, 2],
            [false, -2, -1],
            [false, -2.1, -1.1],
            [false, 1, 1],
            [true, 1, 1, true],
            [false, '1', '1'],
            [true, '1', '1', true],
            [false, '-2', '-2'],
            [true, '-2', '-2', true],
        ];
    }
}