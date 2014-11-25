<?php

namespace SlothsTest\Validation\Validator;

use SlothsTest\TestCase;
use Sloths\Validation\Validator\NumberBetween;

/**
 * @covers Sloths\Validation\Validator\NumberBetween
 */
class NumberBetweenTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $min, $max, $input)
    {
        $this->assertSame($expected, (new NumberBetween($min, $max))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 1, 3, 2],
            [true, 1, 1, 1],
            [true, 1, 2, 2],
            [false, 1, 2, 3],
            [false, 1, 3, false]
        ];
    }

    public function testMessage()
    {
        $this->assertSame('must be a number between 1 and 2', (new NumberBetween(1, 2))->getMessage());
    }
}