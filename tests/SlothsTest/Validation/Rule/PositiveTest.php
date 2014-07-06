<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Positive;

class PositiveTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input)
    {
        $this->assertSame($expected, (new Positive())->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 1],
            [true, 1.1],
            [false, -1],
            [false, 0],
            [false, -1.1],
        ];
    }
}