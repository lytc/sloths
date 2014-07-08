<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\MaxLength;

class MaxLengthTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input, $maxLength)
    {
        $this->assertSame($expected, (new MaxLength($maxLength))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 'foo', 3],
            [true, [1, 2, 3], 3],

            [false, 'foo', 2],
            [false, [1, 2, 3], 2],

            [false, true, 0],
            [false, null, 0],
            [false, 1, 0],
            [false, new \stdClass(), 0],
        ];
    }

    /**
     * @dataProvider invalidMaxValueDataProvider
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidMaxValueShouldThrowAnException($input)
    {
        new MaxLength($input);
    }

    public function invalidMaxValueDataProvider()
    {
        return [
            [-1],
            [1.1],
            [true],
            [false],
            [[]],
            [new \stdClass()]
        ];
    }


    public function testGetGetMessage()
    {
        $rule = new MaxLength(1);
        $this->assertSame('Must have length less than or equal to 1', $rule->getMessage());
    }
}