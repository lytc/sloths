<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Int;

class IntTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input)
    {
        $this->assertSame($expected, (new Int())->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 1],
            [true, 0],
            [true, -1],
            [true, '1'],
            [true, '-1'],
            [false, 1.1],
            [false, '1.1'],
            [false, 2.1],
            [false, 2.0],
            [false, '2.0'],
            [false, true],
            [false, false],
            [false, []],
            [false, new \stdClass()],
        ];
    }
}