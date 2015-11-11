<?php

namespace SlothsTest\Db\Sql\Spec;

use Sloths\Db\Sql\Spec\Value;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Db\Sql\Spec\Value
 */
class ValueTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $values)
    {
        $value = new Value();
        $value->values($values);
        $this->assertSame($expected, $value->toString());
    }

    public function dataProvider()
    {
        return [
            ["`foo` = 'bar', `bar` = 'baz'", ['foo' => 'bar', 'bar' => 'baz']],
        ];
    }
}