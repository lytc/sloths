<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Xdigit;

class XdigitTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input)
    {
        $this->assertSame($expected, (new Xdigit())->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 'ABCDEFabcdef0123456789'],
            [false, 'g'],
            [false, null],
            [false, true],
            [false, ''],
            [false, []],
            [false, new \stdClass()]
        ];
    }
}