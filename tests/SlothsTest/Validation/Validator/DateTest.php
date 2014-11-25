<?php

namespace SlothsTest\Validation\Rule;

use SlothsTest\TestCase;
use Sloths\Validation\Validator\Date;


/**
 * @covers Sloths\Validation\Validator\Date
 */
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
            [false, ''],
            [false, 0],
            [false, 1],
            [false, true],
            [false, null],
            [false, []],
            [false, new \stdClass()],
            [false, time()],
        ];
    }
}