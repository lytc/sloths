<?php

namespace SlothsTest\Validation\Rule;

use SlothsTest\TestCase;
use Sloths\Validation\Validator\Before;

/**
 * @covers Sloths\Validation\Validator\Before
 * @covers Sloths\Validation\Validator\ValidatorTrait
 */
class BeforeTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $comparator, $input, $format = null)
    {
        $this->assertSame($expected, (new Before($comparator, $format))->validate($input));
    }

    public function dataProvider()
    {
        return [
            // DateTime
            [true, new \DateTime('2014-06-06'), new \DateTime('2014-06-05')],
            [false, new \DateTime('2014-06-05'), new \DateTime('2014-06-05')],
            [false, new \DateTime('2014-06-04'), new \DateTime('2014-06-05')],

            // string
            [true, '', '-1 day'],
            [true, '2014-06-06', '2014-06-05'],
            [false, '2014-06-05', '2014-06-05'],
            [false, '2014-06-04', '2014-06-05'],

            [true, '20/08/2014', '2014-08-19', 'd/m/Y']
        ];
    }

    public function testMessage()
    {
        $this->assertSame('must be a date before 20/08/2014', (new Before('20/08/2014', 'd/m/Y'))->getMessage());
    }
}