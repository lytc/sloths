<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Numeric;

class NumericTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input, $allows = '\s')
    {
        $this->assertSame($expected, (new Numeric($allows))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 1],
            [true, '1'],
            [true, 1337],
            [true, 0x539],
            [true, 02471],
            [true, 0b10100111001],
            [true, 1337e0],
            [true, 9.1],
            [true, '-1'],
            [true, '+1'],
            [true, '+1.1'],
            [true, '-1.1'],
            [false, ''],
            [false, null],
            [false, true],
            [false, false],
            [false, new \stdClass()],
            [false, []],
        ];
    }
}