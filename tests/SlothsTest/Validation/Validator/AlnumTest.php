<?php

namespace SlothsTest\Validation\Rule;

use SlothsTest\TestCase;
use Sloths\Validation\Validator\Alnum;

/**
 * @covers Sloths\Validation\Validator\Alnum
 * @covers Sloths\Validation\Validator\ValidatorTrait
 */
class AlnumTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input, $allows = '\s')
    {
        $this->assertSame($expected, (new Alnum($allows))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 'abc123'],
            [true, '123'],
            [true, 123],
            [true, 'abc 123'],
            [false, 'abc 123', ''],
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