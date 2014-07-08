<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Digit;

class DigitTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input)
    {
        $this->assertSame($expected, (new Digit())->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 123],
            [true, '123'],
            [true, '123'],
            [false, ' 123 '],
            [false, 123.1],
            [false, null],
            [false, ''],
        ];
    }
}