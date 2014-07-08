<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Alpha;

class AlphaTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input, $allows = '\s')
    {
        $this->assertSame($expected, (new Alpha($allows))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 'abc'],
            [false, '123'],
            [false, 123],
            [false, 'abc 123'],
            [true, ''],
            [true, ' '],
            [true, "\n"],
            [true, " \n "],
            [false, "\n", ''],
            [false, " \n ", ''],
            [true, "\t"],
            [false, " \t ", ''],
            [false, '@#$'],
            [false, 'abc@#$'],
            [false, '123@#$'],
            [true, 'à'],
            [false, true],
            [false, false],
            [false, null],
            [false, []],
            [false, new \stdClass()],
        ];
    }
}