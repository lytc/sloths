<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Url;

class UrlTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input, $flags = null)
    {
        $this->assertSame($expected, (new Url($flags))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 'http://abc.com'],
            [true, 'http://abc.com/foo'],
            [true, 'https://abc.com/foo'],
            [true, 'https://abc.com/foo?foo=bar'],
            [false, ''],
            [false, '://abc.com'],
            [false, 'http://abc.com', Url::PATH_REQUIRED],
            [false, 'http://abc.com/foo', Url::QUERY_REQUIRED],
            [false, null],
            [false, true],
            [false, 1],
            [false, []],
            [false, new \stdClass()]
        ];
    }
}