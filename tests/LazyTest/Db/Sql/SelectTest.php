<?php

namespace LazyTest\Db\Sql;

use Lazy\Db\Connection;
use Lazy\Db\Sql\Having;
use Lazy\Db\Sql\Join;
use Lazy\Db\Sql\Limit;
use Lazy\Db\Sql\Select;
use Lazy\Db\Sql\Where;
use Lazy\Db\Sql\Group;
use Lazy\Db\Sql\Order;
use Lazy\Db\Statement;
use LazyTest\Db\TestCase;

/**
 * @covers Lazy\Db\Sql\Select
 */
class SelectTest extends TestCase
{
    public function testBasic()
    {
        $select = new Select($this->connection, 'foo');
        $expected = "SELECT * FROM foo";
        $this->assertSame($expected, $select->toString());
    }

    public function testFrom()
    {
        $select = new Select($this->connection);
        $select->from('foo');
        $expected = "SELECT * FROM foo";
        $this->assertSame('foo', $select->from());
        $this->assertSame($expected, $select->toString());
    }

    public function testColumn()
    {
        $select = new Select($this->connection, 'foo');
        $select->column('bar, baz b')
            ->column(array('qux'));

        $this->assertSame(array('bar', 'baz b', 'qux'), $select->column());
        $expected = "SELECT bar, baz b, qux FROM foo";
        $this->assertSame($expected, $select->toString());
    }

    public function testJoin()
    {
        $select = new Select($this->connection, 'foo');
        $select->join('bar', 'bar.foo_id = foo.id');
        $expected = "SELECT * FROM foo INNER JOIN bar ON bar.foo_id = foo.id";

        $join = new Join();
        $join->join('baz', 'baz.foo_id = foo.id');
        $select->join($join);

        $this->assertSame($join, $select->join());
        $expected = "SELECT * FROM foo INNER JOIN baz ON baz.foo_id = foo.id";
        $this->assertSame($expected, $select->toString());
    }

    public function testWhere()
    {
        $connection = $this->connection;
        $select = new Select($connection, 'foo');
        $select->where('bar = ?', 'bar');

        $expected = "SELECT * FROM foo WHERE (bar = 'bar')";
        $this->assertSame($expected, $select->toString());

        $select = new Select($connection, 'foo');
        $where = new Where($connection);
        $where->where('baz = ?', 'baz');
        $select->where($where);
        $this->assertSame($where, $select->where());
        $expected = "SELECT * FROM foo WHERE (baz = 'baz')";
        $this->assertSame($expected, $select->toString());
    }

    public function testGroupBy()
    {
        $select = new Select($this->connection, 'foo');
        $select->group('bar, baz');

        $expected = "SELECT * FROM foo GROUP BY bar, baz";
        $this->assertSame($expected, $select->toString());

        $select = new Select($this->connection, 'foo');
        $group = new Group();
        $group->group('baz, qux');
        $select->group($group);
        $this->assertSame($group, $select->group());
        $expected = "SELECT * FROM foo GROUP BY baz, qux";
        $this->assertSame($expected, $select->toString());
    }

    public function testHaving()
    {
        $connection = $this->connection;
        $select = new Select($connection, 'foo');
        $select->having('bar = :bar', array('bar' => 'bar'));

        $expected = "SELECT * FROM foo HAVING (bar = 'bar')";
        $this->assertSame($expected, $select->toString());

        $select = new Select($connection, 'foo');
        $having = new Having($connection);
        $having->having('baz = :baz', array('baz' => 'baz'));
        $select->having($having);
        $this->assertSame($having, $select->having());
        $expected = "SELECT * FROM foo HAVING (baz = 'baz')";
        $this->assertSame($expected, $select->toString());
    }

    public function testOrderBy()
    {
        $select = new Select($this->connection, 'foo');
        $select->order('foo, bar DESC');

        $expected = "SELECT * FROM foo ORDER BY foo ASC, bar DESC";
        $this->assertSame($expected, $select->toString());

        $select = new Select($this->connection, 'foo');
        $order = new Order();
        $order->order('bar, baz DESC');
        $select->order($order);
        $this->assertSame($order, $select->order());
        $expected = "SELECT * FROM foo ORDER BY bar ASC, baz DESC";
        $this->assertSame($expected, $select->toString());
    }

    public function testLimit()
    {
        $select = new Select($this->connection, 'foo');
        $select->limit(10);

        $expected = "SELECT * FROM foo LIMIT 10";
        $this->assertSame($expected, $select->toString());

        $select = new Select($this->connection, 'foo');
        $limit = new Limit();
        $limit->limit(20);
        $select->limit($limit);
        $this->assertSame($limit, $select->limit());
        $expected = "SELECT * FROM foo LIMIT 20";
        $this->assertSame($expected, $select->toString());
    }

    public function testAliasMethod()
    {
        $select = new Select($this->connection);
        $select->from('foo')
            ->column('bar, baz')
            ->join('bar', 'bar.foo_id = foo.id')
            ->leftJoin('baz', 'baz.foo_id = foo.id')
            ->rightJoin('qux', 'qux.foo_id = foo.id')
            ->where('foo = 1')
            ->orWhere('bar = 2')
            ->group('foo')
            ->group('bar')
            ->having('baz = 3')
            ->orHaving('qux = 4')
            ->order('foo')
            ->order('bar DESC')
            ->limit(10)
            ->offset(2)
        ;

        $expected = "SELECT bar, baz FROM foo ";
        $expected .= "INNER JOIN bar ON bar.foo_id = foo.id LEFT JOIN baz ON baz.foo_id = foo.id RIGHT JOIN qux ON qux.foo_id = foo.id ";
        $expected .= "WHERE (foo = 1) OR (bar = 2) GROUP BY foo, bar HAVING (baz = 3) OR (qux = 4) ORDER BY foo ASC, bar DESC LIMIT 10 OFFSET 2";
        $this->assertSame($expected, $select->toString());
    }

    public function testGetConnection()
    {
        $connection = $this->connection;
        $select = new Select($connection);
        $this->assertSame($connection, $select->getConnection());
    }

    public function testResetColumn()
    {
        $select = new Select($this->connection);
        $select->column('foo, bar');
        $this->assertSame(array('foo', 'bar'), $select->column());
        $select->resetColumn();
        $this->assertSame(array(), $select->column());
    }

    public function testReset()
    {
        $select = new Select($this->connection);
        $select->from('foo')
            ->column('bar, baz')
            ->join('bar', 'bar.foo_id = foo.id')
            ->leftJoin('baz', 'baz.foo_id = foo.id')
            ->rightJoin('qux', 'qux.foo_id = foo.id')
            ->where('foo = 1')
            ->orWhere('bar = 2')
            ->group('foo')
            ->group('bar')
            ->having('baz = 3')
            ->orHaving('qux = 4')
            ->order('foo')
            ->order('bar DESC')
            ->limit(10)
            ->offset(2)
        ;

        $expected = "SELECT bar, baz FROM foo ";
        $expected .= "INNER JOIN bar ON bar.foo_id = foo.id LEFT JOIN baz ON baz.foo_id = foo.id RIGHT JOIN qux ON qux.foo_id = foo.id ";
        $expected .= "WHERE (foo = 1) OR (bar = 2) GROUP BY foo, bar HAVING (baz = 3) OR (qux = 4) ORDER BY foo ASC, bar DESC LIMIT 10 OFFSET 2";
        $this->assertSame($expected, $select->toString());

        $select->reset();
        $this->assertSame("SELECT * FROM foo", $select->toString());
    }

    public function testResetPart()
    {
        $select = new Select($this->connection);
        $select->from('foo')
            ->column('bar, baz')
            ->join('bar', 'bar.foo_id = foo.id')
            ->leftJoin('baz', 'baz.foo_id = foo.id')
            ->rightJoin('qux', 'qux.foo_id = foo.id')
            ->where('foo = 1')
            ->orWhere('bar = 2')
            ->group('foo')
            ->group('bar')
            ->having('baz = 3')
            ->orHaving('qux = 4')
            ->order('foo')
            ->order('bar DESC')
            ->limit(10)
            ->offset(2)
        ;

        $expected = "SELECT bar, baz FROM foo ";
        $expected .= "INNER JOIN bar ON bar.foo_id = foo.id LEFT JOIN baz ON baz.foo_id = foo.id RIGHT JOIN qux ON qux.foo_id = foo.id ";
        $expected .= "WHERE (foo = 1) OR (bar = 2) GROUP BY foo, bar HAVING (baz = 3) OR (qux = 4) ORDER BY foo ASC, bar DESC LIMIT 10 OFFSET 2";
        $this->assertSame($expected, $select->toString());

        $select->resetJoin()->resetWhere()->resetHaving()->resetOrder()->resetGroup()->resetLimit();
        $this->assertSame("SELECT bar, baz FROM foo", $select->toString());
    }

    /**
     * @expectedException \Lazy\Db\Exception
     * @expectedExceptionMessage Call undefined method undefinedMethod
     */
    public function testCallUndefinedMethodShouldThrowAnException()
    {
        $select = new Select($this->connection);
        $select->undefinedMethod();
    }

    public function testQuery()
    {
        $select = new Select($this->connection, 'users');
        $this->assertInstanceOf('\Lazy\Db\Statement', $select->query());
    }

    public function testFetch()
    {
        $select = new Select($this->connection, 'users');
        $this->assertSame(array('id' => '1', 'name' => 'name1'), $select->fetch(\PDO::FETCH_ASSOC));
    }

    public function testFetchAll()
    {
        $select = new Select($this->connection, 'users');
        $select->limit(1);
        $this->assertSame(array(array('id' => '1', 'name' => 'name1')), $select->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function testFetchColumn()
    {
        $select = new Select($this->connection, 'users');
        $this->assertSame('1', $select->fetchColumn());
    }
}