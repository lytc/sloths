<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Blank;

class BlankTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input)
    {
        $this->assertSame($expected, (new Blank())->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, ''],
            [true, '    '],
            [true, " \r\n\t "],
            [false, 'foo'],
            [true, []],
            [false, 0],
            [false, 0.0],
            [false, true],
            [false, false],
            [false, new \stdClass()]
        ];
    }
}