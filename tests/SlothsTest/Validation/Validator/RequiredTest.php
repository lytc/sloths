<?php

namespace SlothsTest\Validation\Validator;

use SlothsTest\TestCase;
use Sloths\Validation\Validator\Required;

/**
 * @covers Sloths\Validation\Validator\Required
 */
class RequiredTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input)
    {
        $this->assertSame($expected, (new Required())->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 1],
            [true, '1'],
            [true, '0'],
            [false, ''],
            [false, null]
        ];
    }
}