<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\StartWith;

class StartWithTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $haystack, $sub, $strict = true)
    {
        $this->assertSame($expected, (new StartWith($sub, $strict))->validate($haystack));
    }

    public function dataProvider()
    {
        return [
            [true, 'foo bar', 'foo'],
            [true, ['foo', 'bar'], 'foo'],
            [true, '1 foo', 1],
            [true, '1.1 foo', 1.1],
            [false, 'foo bar', 'bar'],
            [false, ['foo', 'bar'], 'bar'],
            [true, 'àfoo', 'à'],
            [false, 'foo bar', 'FOO'],
            [true, 'foo bar', 'FOO', false],
            [false, [1, 'bar'], '1'],
            [true, [1, 'bar'], '1', false],
            [false, [], 1],
            [false, true, 1],
            [false, null, 1],
            [false, new \stdClass(), 1],
        ];
    }
}