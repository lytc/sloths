<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\InstOf;

class InstOfTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input, $parent)
    {
        $this->assertSame($expected, (new InstOf($parent))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, new \InvalidArgumentException(), 'Exception'],
            [false, new \stdClass(), 'Exception'],
            [false, true, 'Exception'],
            [false, null, 'Exception'],
            [false, 1, 'Exception'],
            [false, '', 'Exception'],
            [false, [], 'Exception'],
        ];
    }

    /**
     * @dataProvider invalidInstanceDataProvider
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidInstanceShouldThrowAnException($input)
    {
        new InstOf($input);
    }

    public function invalidInstanceDataProvider()
    {
        return [
            [''],
            [null],
            [1],
            [[]],
            [new \stdClass()],
            ['______non___existing___class___']
        ];
    }
}