<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\EndWith;

class EndWithTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $comparator, $input, $strict = true)
    {
        $this->assertSame($expected, (new EndWith($comparator, $strict))->validate($input));
    }

    public function dataProvider()
    {
        return [
            // string
            [true, 'a', 'a'],
            [true, 'c', 'abc'],
            [true, 'c', 'ab c'],
            [true, 1, 'foo 1'],
            [false, '', ''],
            [false, '', 'a'],
            [true, 'à', 'bà'],
            [false, 'C', 'abc'],
            [true, 'C', 'abc', false],

            // array
            [true, 2, [1, 2]],
            [false, '2', [1, 2]],
            [true, '2', [1, 2], false],

            [false, 'c', true],
            [false, 'c', false],
            [false, 'c', null],
            [false, 'c', new \stdClass()]
        ];
    }
}