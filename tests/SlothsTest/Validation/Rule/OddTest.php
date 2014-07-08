<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Odd;

class OddTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input)
    {
        $this->assertSame($expected, (new Odd())->validate($input));
    }

    public function dataProvider()
    {
        return [
            [false, 0],
            [false, 2],
            [false, -2],
            [false, 4],
            [false, '2'],
            [false, '-2'],
            [false, '4'],
            [false, 4.0],
            [false, -4.0],
            [false, '-4.0'],
            [true, 1],
            [true, 3],
            [true, -1],
            [true, -3],
            [true, 2.1],
            [false, true],
            [false, false],
            [false, null],
            [false, []],
            [false, new \stdClass()],
        ];
    }
}