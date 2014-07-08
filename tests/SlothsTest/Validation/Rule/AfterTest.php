<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\After;

class AfterTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $comparator, $input, $format = null)
    {
        $this->assertSame($expected, (new After($comparator, $format))->validate($input));
    }

    public function dataProvider()
    {
        return [
            // DateTime
            [true, new \DateTime('2014-06-05'), new \DateTime('2014-06-06')],
            [false, new \DateTime('2014-06-05'), new \DateTime('2014-06-05')],
            [false, new \DateTime('2014-06-05'), new \DateTime('2014-06-04')],

            // int
            [true, strtotime('2014-06-05'), strtotime('2014-06-06')],
            [false, strtotime('2014-06-05'), strtotime('2014-06-05')],
            [false, strtotime('2014-06-05'), strtotime('2014-06-04')],

            // string
            [true, '', '+1 day'],
            [true, '2014-06-05', '2014-06-06'],
            [false, '2014-06-05', '2014-06-05'],
            [false, '2014-06-05', '2014-06-04'],

            // string with format
            [true, '2014/06/05', '2014/06/06'],
            [true, '2014.06.05', '2014.06.06', 'Y.m.d'],
            [false, '2014|06|05', '2014/06/06', 'Y\|m\|d'],
        ];
    }

    /**
     * @dataProvider invalidDateDataProvider
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidDateShouldThrowAnException($expected, $format = null)
    {
        new After($expected, $format);
    }

    public function invalidDateDataProvider()
    {
        return [
            [true],
            [false],
            [null],
            ['foo'],
            [[]],
            [new \stdClass()],
            ['2014|06|05', 'Y|m|d'],
            ['2014/02/29', 'Y/m/d'],
        ];
    }
}