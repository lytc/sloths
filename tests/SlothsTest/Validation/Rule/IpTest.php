<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Ip;

class IpTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $input, $flags = null)
    {
        $this->assertSame($expected, (new Ip($flags))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, '127.0.0.1'],
            [true, '2607:f0d0:1002:51::4'],
            [false, '2607:f0d0:1002:51::4', Ip::IPV4],
            [false, true],
            [false, false],
            [false, 0],
            [false, 1],
            [false, []],
            [false, new \stdClass()],
        ];
    }
}