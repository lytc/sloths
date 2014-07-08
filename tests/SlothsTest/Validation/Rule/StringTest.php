<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\String;

class StringTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input)
    {
        $this->assertSame($expected, (new String())->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 'foo'],
            [false, []],
            [false, 1],
            [false, true],
            [false, false],
            [false, new \stdClass()],
        ];
    }
}