<?php

namespace SlothsTest\Db\Sql\Spec;

use Sloths\Db\Sql\Spec\Join;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Db\Sql\Spec\Join
 */
class JoinTest extends TestCase
{
    public function test()
    {
        $join = new Join();
        $this->assertSame('', $join->toString());

        $join
            ->inner('bar', 'bar.foo_id = foo.id')
            ->left('baz', 'baz.foo_id = foo.id')
            ->right('qux', 'qux.foo_id = foo.id')
        ;
        $expected = "INNER JOIN bar ON (bar.foo_id = foo.id) LEFT JOIN baz ON (baz.foo_id = foo.id) RIGHT JOIN qux ON (qux.foo_id = foo.id)";
        $this->assertSame($expected, $join->toString());

        $join->reset();
        $this->assertSame('', $join->toString());
    }

    public function testWithCallbackCondition()
    {
        $join = new Join();

        $join->inner('bar', function($join) {
            $join->on('bar.foo_id = foo.id')->and('bar.status > ?', 1);
        });

        $expected = "INNER JOIN bar ON ((bar.foo_id = foo.id) AND (bar.status > 1))";
        $this->assertSame($expected, $join->toString());
    }
}