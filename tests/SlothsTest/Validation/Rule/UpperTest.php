<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Upper;

class UpperTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input)
    {
        $this->assertSame($expected, (new Upper())->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 'ABC'],
            [true, 'ABC123'],
            [true, ' ABC123 '],
            [true, ' 123 '],
            [false, 'abc'],
            [false, ' aBc '],

            [false, 1],
            [false, true],
            [false, []],
            [false, new \stdClass()],
        ];
    }
}