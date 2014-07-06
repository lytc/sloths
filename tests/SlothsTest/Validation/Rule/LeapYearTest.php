<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\LeapYear;

class LeapYearTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input, $allows = '\s')
    {
        $this->assertSame($expected, (new LeapYear($allows))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 2016],
            [true, '2016'],
            [true, new \DateTime('2016-02-29')],
            [true, '2016-01-01'],
            [false, '2016-02-30'], // invalid date
            [false, 2014],
            [false, ''],
            [false, null],
            [false, true],
            [false, false],
            [false, []],
            [false, new \stdClass()],
        ];
    }
}