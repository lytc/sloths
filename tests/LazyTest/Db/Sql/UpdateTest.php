<?php

namespace LazyTest\Db\Sql;

use Lazy\Db\Connection;
use Lazy\Db\Sql\Limit;
use Lazy\Db\Sql\Order;
use Lazy\Db\Sql\Update;
use Lazy\Db\Sql\Where;
use LazyTest\Db\TestCase;

/**
 * @covers Lazy\Db\Sql\Update
 */
class UpdateTest extends TestCase
{
    public function testBasic()
    {
        $update = new Update($this->connection, 'foo');
        $update->data(array('foo' => 'foo', 'bar' => 1));

        $expected = "UPDATE foo SET foo = 'foo', bar = 1";
        $this->assertSame($expected, $update->toString());
    }

    public function testGetConnection()
    {
        $connection = $this->connection;
        $update = new Update($connection);
        $this->assertSame($connection, $update->getConnection());
    }

    public function testFrom()
    {
        $update = new Update($this->connection, 'foo');
        $this->assertSame('foo', $update->from());
        $update->from('bar');
        $this->assertSame('bar', $update->from());
    }

    public function testData()
    {
        $update = new Update($this->connection, 'foo');
        $data = array('foo' => 'foo', 'bar' => 'bar');
        $update->data($data);
        $this->assertSame($data, $update->data());
    }

    public function testWhere()
    {
        $connection = $this->connection;
        $update = new Update($connection, 'foo');
        $update->data(array('bar' => 'bar', 'baz' => 'baz'))->where(array('id' => 1));

        $expected = "UPDATE foo SET bar = 'bar', baz = 'baz' WHERE (id = 1)";
        $this->assertSame($expected, $update->toString());

        $where = new Where($connection);
        $where->where(array('id' => 2));
        $update->where($where);
        $expected = "UPDATE foo SET bar = 'bar', baz = 'baz' WHERE (id = 2)";
        $this->assertSame($expected, $update->toString());
        $this->assertSame($where, $update->where());
    }

    public function testOrder()
    {
        $connection = $this->connection;
        $update = new Update($connection, 'foo');
        $update->data(array('bar' => 'bar', 'baz' => 'baz'))->order('id DESC');

        $expected = "UPDATE foo SET bar = 'bar', baz = 'baz' ORDER BY id DESC";
        $this->assertSame($expected, $update->toString());

        $order = new Order($connection);
        $order->order('id');
        $update->order($order);
        $expected = "UPDATE foo SET bar = 'bar', baz = 'baz' ORDER BY id ASC";
        $this->assertSame($expected, $update->toString());
        $this->assertSame($order, $update->order());
    }

    public function testLimit()
    {
        $connection = $this->connection;
        $update = new Update($connection, 'foo');
        $update->data(array('bar' => 'bar', 'baz' => 'baz'))->limit(10);

        $expected = "UPDATE foo SET bar = 'bar', baz = 'baz' LIMIT 10";
        $this->assertSame($expected, $update->toString());

        $limit = new Limit();
        $limit->limit(20);
        $update->limit($limit);
        $expected = "UPDATE foo SET bar = 'bar', baz = 'baz' LIMIT 20";
        $this->assertSame($expected, $update->toString());
        $this->assertSame($limit, $update->limit());
    }

    public function testComplex()
    {
        $update = new Update($this->connection, 'foo');
        $update->data(array('foo' => 'foo', 'bar' => 1))
            ->where('foo IN(?)', array(array(1, 2, 3)))
            ->orWhere('bar = ?', 'bar')
            ->order('foo')
            ->limit(10);

        $expected = "UPDATE foo SET foo = 'foo', bar = 1 WHERE (foo IN(1, 2, 3)) OR (bar = 'bar') ORDER BY foo ASC LIMIT 10";
        $this->assertSame($expected, $update->toString());
    }

    public function testReset()
    {
        $update = new Update($this->connection, 'foo');
        $update->data(array('foo' => 'foo', 'bar' => 1))
            ->where('foo IN(?)', array(array(1, 2, 3)))
            ->order('foo')
            ->limit(10);

        $expected = "UPDATE foo SET foo = 'foo', bar = 1 WHERE (foo IN(1, 2, 3)) ORDER BY foo ASC LIMIT 10";
        $this->assertSame($expected, $update->toString());

        $update->reset();
        $expected = "UPDATE foo SET ";
        $this->assertSame($expected, $update->toString());
    }

    public function testResetPart()
    {
        $update = new Update($this->connection, 'foo');
        $update->data(array('foo' => 'foo', 'bar' => 1))
            ->where('foo IN(?)', array(array(1, 2, 3)))
            ->order('foo')
            ->limit(10);

        $expected = "UPDATE foo SET foo = 'foo', bar = 1 WHERE (foo IN(1, 2, 3)) ORDER BY foo ASC LIMIT 10";
        $this->assertSame($expected, $update->toString());

        $update->resetWhere()->resetOrder()->resetLimit();
        $expected = "UPDATE foo SET foo = 'foo', bar = 1";
        $this->assertSame($expected, $update->toString());
    }

    public function testExec()
    {
        $connection = $this->getMockConnection(array('exec', 'quote'));

        $connection->expects($this->once())
            ->method('exec')
            ->with($this->equalTo("UPDATE users SET name = 'name' LIMIT 1"))
            ->will($this->returnValue(1));

        $connection->expects($this->once())
            ->method('quote')
            ->with('name')
            ->will($this->returnValue("'name'"));

        $update = new Update($connection, 'users');
        $update->data(array('name' => 'name'))->limit(1);
        $update->exec();
    }

    /**
     * @expectedException \Lazy\Db\Exception
     * @expectedExceptionMessage Call undefined method undefinedMethod
     */
    public function testCallUndefinedMethodShouldThrowAnException()
    {
        $delete = new Update($this->connection);
        $delete->undefinedMethod();
    }
}