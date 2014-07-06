<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Length;

class LengthTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input, $length = 0)
    {
        $this->assertSame($expected, (new Length($length))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, '', 0],
            [true, 'a', 1],
            [true, ' a ', 3],
            [true, [], 0],
            [true, [1], 1],
            [true, [1, 2], 2],
            [false, [], 1],

            [false, true],
            [false, null],
            [false, new \stdClass()],
        ];
    }

    /**
     * @dataProvider invalidLengthDataProvider
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidLengthShouldThrowAnException($input)
    {
        new Length($input);
    }

    public function invalidLengthDataProvider()
    {
        return [
            [''],
            ['foo'],
            [null],
            [true],
            [[]],
            [new \stdClass()],
            [-1],
            [1.1],
        ];
    }
}