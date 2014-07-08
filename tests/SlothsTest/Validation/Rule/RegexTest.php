<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Regex;

class RegexTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input)
    {
        $this->assertSame($expected, (new Regex())->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, '//'],
            [true, '/foo/iusm'],
            [true, '#foo#'],
            [true, '/\w+/'],
            [false, '/foo/#'],
            [false, '/foo#'],
            [false, true],
            [false, null],
            [false, 1],
            [false, []],
            [false, new \stdClass()]
        ];
    }
}