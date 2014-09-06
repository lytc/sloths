<?php

namespace SlothsTest\Db\Sql\Spec;

use Sloths\Db\Sql\Spec\Set;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Db\Sql\Spec\Set
 */
class SetTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $values)
    {
        $set = new Set();
        $set->values($values);
        $this->assertSame($expected, $set->toString());
    }

    public function dataProvider()
    {
        return [
            ["SET foo = 'bar', bar = 'baz'", ['foo' => 'bar', 'bar' => 'baz']],
        ];
    }
}