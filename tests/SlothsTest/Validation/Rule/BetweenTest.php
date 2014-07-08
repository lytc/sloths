<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Between;

class BetweenTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $min, $input, $max, $inclusive = true)
    {
        $this->assertSame($expected, (new Between($min, $max, $inclusive))->validate($input));
    }

    public function dataProvider()
    {
        return [
            // scalar
            [true, 1, 2, 3],
            [true, 1, 2, 2],
            [false, 1, 2, 2, false],
            [true, -3, -2, -1],
            [true, -3, -2, -2],
            [false, -3, -2, -2, false],
            [true, 1.1, 2.1, 3.1],
            [true, 1.1, 2.1, 2.1],
            [false, 1.1, 2.1, 2.1, false],
            [true, 'a', 'b', 'c'],
            [true, 'a', 'b', 'b'],
            [false, 'a', 'b', 'b', false],

            // datetime
            [true, new \DateTime(), new \DateTime('+1 day'), new \DateTime('+2 day')],
            [true, new \DateTime(), new \DateTime('+1 day'), new \DateTime('+1 day')],
            [false, new \DateTime(), new \DateTime('+1 day'), new \DateTime('+1 day'), false],
        ];
    }

    /**
     * @dataProvider invalidMinMaxDataProvider
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidMinMaxShouldThrowAnException($min, $max)
    {
        new Between($min, $max);
    }

    public function invalidMinMaxDataProvider()
    {
        return [
            [2, 1],
            ['b', 'a'],
            [new \DateTime('+1 day'), new \DateTime()]
        ];
    }
}