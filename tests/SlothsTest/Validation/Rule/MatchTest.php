<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\Match;

class MatchTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $pattern, $input)
    {
        $this->assertSame($expected, (new Match($pattern))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, '/foo/', 'foobar'],
            [true, '/^foo/', 'foobar'],
            [true, '/^1$/', 1],
            [false, '/^foo/', 'bar'],
            [false, '/foo/', 1],
            [false, '/foo/', true],
            [false, '/foo/', null],
            [false, '/foo/', []],
            [false, '/foo/', new \stdClass()],
        ];
    }

    /**
     * @dataProvider invalidPatternDataProvider
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidPatternShouldThrowAnException($input)
    {
        new Match($input);
    }

    public function invalidPatternDataProvider()
    {
        return [
            [1],
            [true],
            [null],
            [[]],
            [new \stdClass()],
            ['foo'],
            ['/foo']
        ];
    }
}