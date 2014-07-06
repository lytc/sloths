<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Null;

class NullTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input)
    {
        $this->assertSame($expected, (new Null())->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, null],
            [false, true],
            [false, ''],
            [false, 'foo'],
            [false, 1],
            [false, []],
            [false, new \stdClass()],
        ];
    }
}