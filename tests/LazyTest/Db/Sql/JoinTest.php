<?php

namespace LazyTest\Db\Sql;

use Lazy\Db\Sql\Join;

/**
 * @covers Lazy\Db\Sql\Join
 */
class JoinTest extends \PHPUnit_Framework_TestCase
{
    public function testWithPureString()
    {
        $join = new Join('bar ON bar.foo_id = foo.id');
        $expected = "INNER JOIN bar ON bar.foo_id = foo.id";
        $this->assertSame(array(array('type' => Join::INNER,
            'tableName' => 'bar ON bar.foo_id = foo.id', 'conditions' => null)), $join->join());
        $this->assertSame($expected, $join->toString());
    }

    public function testWithTableAndCondition()
    {
        $join = new Join('bar', 'bar.foo_id = foo.id');
        $expected = "INNER JOIN bar ON bar.foo_id = foo.id";
        $this->assertSame($expected, $join->toString());
    }

    public function testWithTableAndTableAliasAndCondition()
    {
        $join = new Join('bar baz', 'baz.foo_id = foo.id');
        $expected = "INNER JOIN bar baz ON baz.foo_id = foo.id";
        $this->assertSame($expected, $join->toString());
    }

    public function testLeftJoin()
    {
        $join = new Join();
        $join->leftJoin('bar baz', 'baz.foo_id = foo.id');
        $expected = "LEFT JOIN bar baz ON baz.foo_id = foo.id";
        $this->assertSame($expected, $join->toString());
    }

    public function testRightJoin()
    {
        $join = new Join();
        $join->rightJoin('bar baz', 'baz.foo_id = foo.id');
        $expected = "RIGHT JOIN bar baz ON baz.foo_id = foo.id";
        $this->assertSame($expected, $join->toString());
    }

    public function testMultipleJoin()
    {
        $join = new Join('bar ON bar.foo_id = foo.id');
        $join->join('baz', 'baz.bar_id = bar.id')
            ->leftJoin('qux q', 'q.foo_id = foo.id')
            ->rightJoin('bazz', 'bazz.foo_id = foo.id');
        $expected = "INNER JOIN bar ON bar.foo_id = foo.id INNER JOIN baz ON baz.bar_id = bar.id LEFT JOIN qux q ON q.foo_id = foo.id RIGHT JOIN bazz ON bazz.foo_id = foo.id";
        $this->assertSame($expected, $join->toString());
    }

    public function testJoinWithJoinInstance()
    {
        $join = new Join('bar', 'bar.foo_id = foo.id');
        $join2 = new Join();
        $join2->leftJoin('baz', 'baz.foo_id = foo.id');
        $join->join($join2);
        $expected = "INNER JOIN bar ON bar.foo_id = foo.id LEFT JOIN baz ON baz.foo_id = foo.id";
        $this->assertSame($expected, $join->toString());
    }

    public function testReset()
    {
        $join = new Join('bar ON bar.foo_id = foo.id');
        $join->reset();
        $this->assertSame('', $join->toString());
    }
}