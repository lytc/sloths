<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Domain;

class DomainTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input)
    {
        return $this->assertSame($expected, (new Domain())->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 'example.com'],
            [true, 'example.com.vn'],
            [false, '_example.com'],
            [false, 'exam_ple.com'],
            [false, 'foo'],
            [false, true],
            [false, 1],
            [false, []],
            [false, null],
            [false, new \stdClass()]
        ];
    }
}