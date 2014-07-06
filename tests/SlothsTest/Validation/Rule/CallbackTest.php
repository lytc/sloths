<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Callback;

class CallbackTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input, $syntaxOnly = false)
    {
        $this->assertSame($expected, (new Callback($syntaxOnly))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, function() {}],
            [true, [new \Exception(), 'getCode']],
            [true, ['____foo', 'bar'], true],
            [false, ['____foo', 'bar']]
        ];
    }
}