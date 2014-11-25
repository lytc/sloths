<?php

namespace SlothsTest\Validation\Validator;

use SlothsTest\TestCase;
use Sloths\Validation\Validator\DateFormat;

/**
 * @covers Sloths\Validation\Validator\DateFormat
 */
class DateFormatTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $format, $input)
    {
        $this->assertSame($expected, (new DateFormat($format))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 'Y-m-d', '2014-08-22'],
            [true, 'd/m/Y', '22/08/2014'],
            [false, 'Y-m-d', '22/08/2014'],
            [false, 'Y-m-d', '2014-22-08'],
            [false, 'Y-m-d', 1]
        ];
    }

    public function testMessage()
    {
        $this->assertSame('must match the format d/m/Y', (new DateFormat('d/m/Y'))->getMessage());
    }
}