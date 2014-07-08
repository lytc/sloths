<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Object;

class ObjectTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input)
    {
        $this->assertSame($expected, (new Object())->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, new \stdClass()],
            [false, null],
            [false, true],
            [false, ''],
            [false, 'foo'],
            [false, 1],
            [false, []],
        ];
    }
}