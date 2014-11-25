<?php

namespace SlothsTest\Validation\Validator;

use SlothsTest\TestCase;
use Sloths\Validation\Validator\DateBetween;

/**
 * @covers Sloths\Validation\Validator\DateBetween
 */
class DateBetweenTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $min, $max, $format, $input)
    {
        $this->assertSame($expected, (new DateBetween($min, $max, $format))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, '2014-08-20', '2014-08-22', null, '2014-08-21'],
            [true, '2014-08-20', '2014-08-20', null, '2014-08-20'],
            [true, '2014-08-20', '2014-08-21', null, '2014-08-21'],
            [true, new \DateTime('2014-08-20'), new \DateTime('2014-08-22'), null, new \DateTime('2014-08-21')],
            [true, new \DateTime('2014-08-20'), new \DateTime('2014-08-22'), null, '2014-08-21'],
            [false, '2014-08-20', '2014-08-22', null, 'foo'],

            [true, '20/08/2014', '22/08/2014', 'd/m/Y', '2014-08-21'],
        ];
    }

    public function testGetMessage()
    {
        $this->assertSame('must be a date between 20/08/2014 and 21/08/2014',
            (new DateBetween('20/08/2014', '21/08/2014', 'd/m/Y'))->getMessage());
    }
}