<?php

namespace SlothsTest\Validation\Rule;

use SlothsTest\TestCase;
use Sloths\Validation\Validator\Email;


/**
 * @covers Sloths\Validation\Validator\Email
 */
class EmailTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input)
    {
        $this->assertSame($expected, (new Email())->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 'foo@example.com'],
            [true, 'foo@example.com.vn'],
            [false, 'foo'],
            [false, 1],
            [false, true],
            [false, null],
            [false, []],
            [false, new \stdClass()],
        ];
    }
}