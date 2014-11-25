<?php

namespace SlothsTest\Validation\Validator;

use SlothsTest\TestCase;
use Sloths\Validation\Validator\LessThan;

/**
 * @covers Sloths\Validation\Validator\LessThan
 */
class LessThanTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $value, $input)
    {
        $this->assertSame($expected, (new LessThan($value))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 2, 1],
            [true, 'b', 'a'],
            [false, 1, 2],
            [false, 'a', 'b']
        ];
    }

    public function testMessage()
    {
        $this->assertSame('must be less than 1', (new LessThan(1))->getMessage());
    }
}