<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\HasKey;

class HasKeyTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $key, $input)
    {
        $this->assertSame($expected, (new HasKey($key))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 1, [1 => 2]],
            [true, -1, [-1 => 2]],
            [true, '1.1', ['1.1' => 2]],
            [true, 'foo', ['foo' => 'bar']],
            [true, 2, [1, 3, 4]],

            [false, 1, []],
            [false, 1, true],
            [false, 1, null],
            [false, 1, ''],
            [false, 1, new \stdClass()],
        ];
    }

    /**
     * @dataProvider invalidKeyDataProvider
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidKeyShouldThrowAnException($key)
    {
        new HasKey($key);
    }

    public function invalidKeyDataProvider()
    {
        return [
            [true],
            [false],
            [null],
            [1.1],
            [[]],
            [new \stdClass()],
        ];
    }
}