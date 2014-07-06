<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\GreaterThan;

class GreaterThanTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $comparator, $input)
    {
        $this->assertSame($expected, (new GreaterThan($comparator))->validate($input));
    }

    public function dataProvider()
    {
        return [
            // scalar
            [true, 1, 2],
            [false, 2, 1],
            [true, -2, -1],
            [false, -1, -2],
            [true, 1.1, 2.1],
            [false, 2.1, 1.1],
            [true, 'a', 'b'],
            [false, 'b', 'a'],
            [false, 1, 1],
            [false, 'a', 'a'],

            // datetime
            [true, new \DateTime(), new \DateTime('+1 day')],
            [false, new \DateTime('+1 day'), new \DateTime()],
            [false, new \DateTime(), new \DateTime()],
        ];
    }
}