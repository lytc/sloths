<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Even;

class EvenTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input)
    {
        $this->assertSame($expected, (new Even())->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 0],
            [true, 2],
            [true, -2],
            [true, 4],
            [true, '2'],
            [true, '-2'],
            [true, '4'],
            [true, 4.0],
            [true, -4.0],
            [true, '-4.0'],
            [false, 1],
            [false, -1],
            [false, -3],
            [false, 2.1],
            [false, true],
            [false, false],
            [false, null],
            [false, []],
            [false, new \stdClass()],
        ];
    }
}