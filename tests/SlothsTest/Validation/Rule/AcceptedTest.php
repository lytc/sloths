<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Accepted;

class AcceptedTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input, $flags = null)
    {
        $this->assertSame($expected, (new Accepted($flags))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 1],
            [true, true],
            [true, 'on'],
            [true, 'yes'],

            [false, 0],
            [false, false],
            [false, 'off'],
            [false, 'no'],
            [false, 'foo'],

            [false, 0, Accepted::NULL_ON_FAILURE],
            [false, false, Accepted::NULL_ON_FAILURE],
            [false, 'off', Accepted::NULL_ON_FAILURE],
            [false, 'no', Accepted::NULL_ON_FAILURE],
            [true, 'foo', Accepted::NULL_ON_FAILURE],
        ];
    }
}