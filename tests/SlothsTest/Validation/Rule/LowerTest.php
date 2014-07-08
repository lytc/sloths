<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Lower;

class LowerTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input)
    {
        $this->assertSame($expected, (new Lower())->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 'abc'],
            [true, 'abc123'],
            [true, ' abc123 '],
            [true, ' 123 '],
            [false, 'aBc'],
            [false, ' aBc '],

            [false, 1],
            [false, true],
            [false, []],
            [false, new \stdClass()],
        ];
    }
}