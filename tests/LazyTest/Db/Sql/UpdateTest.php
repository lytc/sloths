<?php

namespace LazyTest\Db\Sql;

use Lazy\Db\Sql\Update;

class UpdateTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $update = new Update('foo');
        $update->set(['foo' => 'bar', 'bar' => 'baz'])
            ->where(['baz' => 'qux']);

        $expected = "UPDATE foo SET foo = 'bar', bar = 'baz' WHERE (baz = 'qux')";
        $this->assertSame($expected, $update->toString());
    }

    public function testIgnore()
    {
        $update = new Update('foo');
        $update->ignore()->set(['foo' => 'bar']);

        $expected = "UPDATE IGNORE foo SET foo = 'bar'";
        $this->assertSame($expected, $update->toString());
    }

    public function testLimit()
    {
        $update = new Update('foo');
        $update->set(['foo' => 'bar'])->limit(1);

        $expected = "UPDATE foo SET foo = 'bar' LIMIT 1";
        $this->assertSame($expected, $update->toString());
    }

    public function testGetWhere()
    {
        $update = new Update();
        $this->assertInstanceOf('Lazy\Db\Sql\Where', $update->where());
        $this->assertInstanceOf('Lazy\Db\Sql\Where', $update->orWhere());
    }

    public function testOrWhere()
    {
        $update = new Update('foo');
        $update->set(['foo' => 'bar']);
        $update->where("foo = 'bar'")->orWhere("bar = 'baz'");
        $expected = "UPDATE foo SET foo = 'bar' WHERE (foo = 'bar') OR (bar = 'baz')";
        $this->assertSame($expected, $update->toString());
    }
}