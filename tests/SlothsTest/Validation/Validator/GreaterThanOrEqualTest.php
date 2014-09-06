<?php

namespace SlothsTest\Validation\Validator;

use SlothsTest\TestCase;
use Sloths\Validation\Validator\GreaterThanOrEqual;

/**
 * @covers Sloths\Validation\Validator\GreaterThanOrEqual
 */
class GreaterThanOrEqualTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $value, $input)
    {
        $this->assertSame($expected, (new GreaterThanOrEqual($value))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 1, 2],
            [true, 1, 1],
            [true, 'a', 'b'],
            [true, 'a', 'a'],
        ];
    }

    public function testMessage()
    {
        $this->assertSame('must be greater than or equal to 1', (new GreaterThanOrEqual(1))->getMessage());
    }
}