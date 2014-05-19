<?php

namespace SlothsTest\Db\Sql;
use Sloths\Db\Sql\Insert;
use Sloths\Db\Sql\Select;

class InsertTest extends \PHPUnit_Framework_TestCase
{
    public function testColumns()
    {
        $insert = new Insert('foo');
        $insert->columns('foo, bar')->values(['foo', 'bar']);

        $expected = "INSERT INTO foo (foo, bar) VALUES ('foo', 'bar')";
        $this->assertSame($expected, $insert->toString());
    }

    public function testColumnAutoGetFromValuesKey()
    {
        $insert = new Insert('foo');
        $insert->values(['foo' => 'foo', 'bar' => 'bar']);

        $expected = "INSERT INTO foo (foo, bar) VALUES ('foo', 'bar')";
        $this->assertSame($expected, $insert->toString());
    }

    public function testMultipleValues()
    {
        $insert = new Insert('foo');
        $insert->values([['foo' => 'foo'], ['foo' => 'bar']]);
        $expected = "INSERT INTO foo (foo) VALUES ('foo'), ('bar')";
        $this->assertSame($expected, $insert->toString());
    }

    public function testSelect()
    {

        $select = new Select('bar');
        $select->select('foo, bar');

        $insert = new Insert('foo');
        $insert->columns('foo, bar')->select($select);

        $expected = "INSERT INTO foo (foo, bar) SELECT foo, bar FROM bar";
        $this->assertSame($expected, $insert->toString());
    }

    public function testIgnore()
    {
        $insert = new Insert('foo');
        $insert->ignore()->values(['foo' => 'foo']);

        $expected = "INSERT IGNORE INTO foo (foo) VALUES ('foo')";
        $this->assertSame($expected, $insert->toString());
    }

    public function testOnDuplicateKeyUpdate()
    {
        $insert = new Insert('foo');
        $insert->values(['foo' => 'foo'])->onDuplicateKeyUpdate(['foo' => 'bar', 'bar' => 'baz']);
        $expected = "INSERT INTO foo (foo) VALUES ('foo') ON DUPLICATE KEY UPDATE foo = 'bar', bar = 'baz'";
        $this->assertSame($expected, $insert->toString());
    }
}