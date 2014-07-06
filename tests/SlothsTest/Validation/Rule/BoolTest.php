<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Bool;

class BoolTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input)
    {
        $this->assertSame($expected, (new Bool())->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, true],
            [true, false],
            [false, 1],
            [false, 'a'],
            [false, []],
            [false, new \stdClass()],
        ];
    }
}