<?php

namespace SlothsTest\Validation\Validator;

use SlothsTest\TestCase;
use Sloths\Validation\Validator\GreaterThan;

/**
 * @covers Sloths\Validation\Validator\GreaterThan
 */
class GreaterThanTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $value, $input)
    {
        $this->assertSame($expected, (new GreaterThan($value))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 1, 2],
            [true, 'a', 'b'],
            [false, 2, 1],
            [false, 'b', 'a'],
        ];
    }

    public function testMessage()
    {
        $this->assertSame('must be greater than 1', (new GreaterThan(1))->getMessage());
    }
}