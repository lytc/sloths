<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Date;

class DateTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input, $format = null)
    {
        $this->assertSame($expected, (new Date($format))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, new \DateTime()],
            [true, '2014-06-05'],
            [false, '2014.06.05'],
            [true, '2014.06.05', 'Y.m.d'],
            [false, ''],
            [false, 0],
            [false, 1],
            [false, true],
            [false, null],
            [false, []],
            [false, new \stdClass()],
            [false, time()]
        ];
    }
}