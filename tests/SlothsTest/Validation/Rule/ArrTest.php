<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Arr;

class ArrTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input)
    {
        $this->assertSame($expected, (new Arr())->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, []],
            [false, 1],
            [false, true],
            [false, false],
            [false, ''],
            [false, 'foo'],
            [false, new \stdClass()],
        ];
    }
}