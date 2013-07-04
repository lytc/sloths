<?php

namespace LazyTest\Db\Sql;

use Lazy\Db\Connection;
use Lazy\Db\Sql\Insert;
use LazyTest\Db\TestCase;

/**
 * @covers Lazy\Db\Sql\Insert
 */
class InsertTest extends TestCase
{
    public function testWithSingleValue()
    {
        $insert = new Insert($this->connection, 'foo');
        $insert->value(array('foo' => 'foo', 'bar' => 1));
        $expected = "INSERT INTO foo (foo, bar) VALUES ('foo', 1)";
        $this->assertSame($expected, $insert->toString());
    }

    public function testWithMultipleValue()
    {
        $insert = new Insert($this->connection, 'foo');
        $insert->value(array(
            array('foo' => 'foo', 'bar' => 1),
            array('foo' => 'foo2', 'bar' => 2),
        ));
        $expected = "INSERT INTO foo (foo, bar) VALUES ('foo', 1), ('foo2', 2)";
        $this->assertSame($expected, $insert->toString());
    }

    public function testWithColumn()
    {
        $insert = new Insert($this->connection, 'foo');
        $insert->column('foo, bar')
            ->value(array('foo', 1));

        $expected = "INSERT INTO foo (foo, bar) VALUES ('foo', 1)";
        $this->assertSame($expected, $insert->toString());

        $insert = new Insert($this->connection, 'foo');
        $insert->column('foo, bar')
            ->value(array(array('foo', 1), array('foo2', 2)));

        $expected = "INSERT INTO foo (foo, bar) VALUES ('foo', 1), ('foo2', 2)";
        $this->assertSame($expected, $insert->toString());
    }

    public function testGetConnection()
    {
        $connection = $this->connection;
        $insert = new Insert($connection);
        $this->assertSame($connection, $insert->getConnection());
    }

    public function testInto()
    {
        $insert = new Insert($this->connection);
        $insert->into('foo');
        $this->assertSame('foo', $insert->into());
    }

    public function testColumn()
    {
        $insert = new Insert($this->connection);
        $insert->column('foo, bar');
        $this->assertSame(array('foo', 'bar'), $insert->column());

        $insert->column(array('baz', 'qux'));
        $this->assertSame(array('baz', 'qux'), $insert->column());
    }

    public function testGetValue()
    {
        $insert = new Insert($this->connection);
        $values = array('foo' => 'foo', 'bar' => 'bar');
        $insert->value($values);
        $this->assertSame($values, $insert->value());
    }

    public function testReset()
    {
        $insert = new Insert($this->connection);
        $insert->column(array('foo', 'bar'))->value(array('foo', 'bar'));
        $insert->reset();
        $this->assertSame(array(), $insert->column());
        $this->assertSame(array(), $insert->value());
    }

    public function testExec()
    {
        $mockConnection = $this->getMockConnection(array('exec'));

        $mockConnection->expects($this->once())
            ->method('exec')
            ->with($this->equalTo("INSERT INTO users (name) VALUES ('name')"))
            ->will($this->returnValue(1));

        $insert = new Insert($mockConnection, 'users');
        $insert->value(array('name' => 'name'));

        $this->assertSame(1, $insert->exec());
    }
}