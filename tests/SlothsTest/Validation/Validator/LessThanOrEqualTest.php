<?php

namespace SlothsTest\Validation\Validator;

use SlothsTest\TestCase;
use Sloths\Validation\Validator\LessThanOrEqual;

/**
 * @covers Sloths\Validation\Validator\LessThanOrEqual
 */
class LessThanOrEqualTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $value, $input)
    {
        $this->assertSame($expected, (new LessThanOrEqual($value))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 2, 1],
            [true, 1, 1],
            [true, 'b', 'a'],
            [true, 'a', 'a'],
        ];
    }

    public function testMessage()
    {
        $this->assertSame('must be less than or equal to 1', (new LessThanOrEqual(1))->getMessage());
    }
}