<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Float;

class FloatTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input)
    {
        $this->assertSame($expected, (new Float())->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 1.0],
            [true, 1.1],
            [true, -1.1],
            [true, '-1.0'],
            [true, '-1.1'],
            [true, '-1.1'],
            [true, .1],
            [true, -.1],
            [true, '.1'],
            [true, '-.1'],
            [true, 1e7],
            [false, 0],
            [false, 1],
            [false, '1'],
            [false, true],
            [false, false],
            [false, []],
            [false, new \stdClass()],
        ];
    }
}