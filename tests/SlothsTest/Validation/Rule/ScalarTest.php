<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Scalar;

class ScalarTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input)
    {
        $this->assertSame($expected, (new Scalar())->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, ''],
            [true, 'foo'],
            [true, 1],
            [true, -1],
            [true, 0x1],
            [true, true],
            [true, false],
            [false, []],
            [false, new \stdClass()],
        ];
    }
}