<?php

namespace LazyTest\Db\Sql;

use Lazy\Db\Sql\Delete;
use Lazy\Db\Sql\Order;
use Lazy\Db\Sql\Limit;
use Lazy\Db\Sql\Where;
use Lazy\Db\Connection;
use LazyTest\Db\TestCase;

/**
 * @covers Lazy\Db\Sql\Delete
 */
class DeleteTest extends TestCase
{
    public function testBasic()
    {
        $delete = new Delete($this->connection, 'foo');
        $expected = "DELETE FROM foo";
        $this->assertSame($expected, $delete->toString());
    }

    public function testGetConnection()
    {
        $connection = $this->connection;
        $delete = new Delete($connection, 'foo');
        $this->assertSame($connection, $delete->getConnection());
    }

    public function testFrom()
    {
        $delete = new Delete($this->connection);
        $delete->from('foo');
        $this->assertSame('foo', $delete->from());
        $expected = "DELETE FROM foo";
        $this->assertSame($expected, $delete->toString());
    }

    public function testWhere()
    {
        $delete = new Delete($this->connection, 'foo');
        $delete->where('foo = ?', 'foo');
        $expected = "DELETE FROM foo WHERE (foo = 'foo')";
        $this->assertSame($expected, $delete->toString());

        $where = new Where($this->connection);
        $where->where('bar = ?', 'bar');
        $delete->where($where);
        $expected = "DELETE FROM foo WHERE (bar = 'bar')";
        $this->assertSame($expected, $delete->toString());
    }

    public function testOrder()
    {
        $delete = new Delete($this->connection, 'foo');
        $delete->order('bar');
        $expected = "DELETE FROM foo ORDER BY bar ASC";
        $this->assertSame($expected, $delete->toString());

        $order = new Order();
        $order->order('baz DESC');
        $delete->order($order);
        $expected = "DELETE FROM foo ORDER BY baz DESC";
        $this->assertSame($expected, $delete->toString());
        $this->assertSame($order, $delete->order());
    }

    public function testLimit()
    {
        $delete = new Delete($this->connection, 'foo');
        $delete->limit(10);
        $expected = "DELETE FROM foo LIMIT 10";
        $this->assertSame($expected, $delete->toString());

        $limit = new Limit();
        $limit->limit(20);
        $delete->limit($limit);
        $expected = "DELETE FROM foo LIMIT 20";
        $this->assertSame($expected, $delete->toString());
        $this->assertSame($limit, $delete->limit());
    }

    public function testComplex()
    {
        $delete = new Delete($this->connection);
        $delete->from('foo')
            ->where('foo = ?', 1)
            ->orWhere('bar = ?', 2)
            ->order('bar DESC')
            ->limit(10);

        $expected = "DELETE FROM foo WHERE (foo = 1) OR (bar = 2) ORDER BY bar DESC LIMIT 10";
        $this->assertSame($expected, $delete->toString());
    }

    public function testReset()
    {
        $delete = new Delete($this->connection);
        $delete->from('foo')
            ->where('foo = ?', 1)
            ->orWhere('bar = ?', 2)
            ->order('bar DESC')
            ->limit(10);

        $expected = "DELETE FROM foo WHERE (foo = 1) OR (bar = 2) ORDER BY bar DESC LIMIT 10";
        $this->assertSame($expected, $delete->toString());

        $delete->reset();
        $expected = "DELETE FROM foo";
        $this->assertSame($expected, $delete->toString());
    }

    public function testResetPart()
    {
        $delete = new Delete($this->connection);
        $delete->from('foo')
            ->where('foo = ?', 1)
            ->orWhere('bar = ?', 2)
            ->order('bar DESC')
            ->limit(10);

        $expected = "DELETE FROM foo WHERE (foo = 1) OR (bar = 2) ORDER BY bar DESC LIMIT 10";
        $this->assertSame($expected, $delete->toString());

        $delete->resetWhere()->resetOrder()->resetLimit();
        $expected = "DELETE FROM foo";
        $this->assertSame($expected, $delete->toString());
    }

    /**
     * @expectedException \Lazy\Db\Exception
     * @expectedExceptionMessage Call undefined method undefinedMethod
     */
    public function testCallUndefinedMethodShouldThrowAnException()
    {
        $delete = new Delete($this->connection);
        $delete->undefinedMethod();
    }

    public function testExec()
    {
        $connection = $this->getMockConnection(array('exec'));
        $connection->expects($this->once())
            ->method('exec')
            ->with($this->equalTo("DELETE FROM users LIMIT 2"))
            ->will($this->returnValue(2));

        $delete = new Delete($connection, 'users');
        $delete->limit(2);
        $this->assertSame(2, $delete->exec());
    }
}