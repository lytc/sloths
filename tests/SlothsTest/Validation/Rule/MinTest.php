<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Min;

class MinTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $max,  $input, $inclusive = false)
    {
        $this->assertSame($expected, (new Min($max, $inclusive))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [false, 2, 1],
            [false, -1, -2],
            [false, 2.1, 1.1],
            [false, -1.1, -2.1],
            [false, '2', '1'],
            [false, '-1', '-2'],
            [false, '-1.1', '-2.1'],
            [true, 1, 2],
            [true, -2, -1],
            [true, -2.1, -1.1],
            [false, 1, 1],
            [true, 1, 1, true],
            [false, '1', '1'],
            [true, '1', '1', true],
            [false, '-2', '-2'],
            [true, '-2', '-2', true],
        ];
    }
}