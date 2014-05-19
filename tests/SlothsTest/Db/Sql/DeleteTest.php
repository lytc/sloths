<?php

namespace SlothsTest\Db\Sql;

use Sloths\Db\Sql\Delete;

class DeleteTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $delete = new Delete('foo');
        $delete->where("bar = 'baz'");

        $expected = "DELETE FROM foo WHERE (bar = 'baz')";
        $this->assertSame($expected, $delete->toString());
    }

    public function testIgnore()
    {
        $delete = new Delete('foo');
        $delete->ignore()->where("bar = 'baz'");

        $expected = "DELETE IGNORE FROM foo WHERE (bar = 'baz')";
        $this->assertSame($expected, $delete->toString());
    }

    public function testGetWhere()
    {
        $delete = new Delete();
        $this->assertInstanceOf('Sloths\Db\Sql\Where', $delete->where());
        $this->assertInstanceOf('Sloths\Db\Sql\Where', $delete->orWhere());
    }

    public function testOrWhere()
    {
        $delete = new Delete('foo');
        $delete->where("foo = 'bar'")->orWhere("bar = 'baz'");
        $expected = "DELETE FROM foo WHERE (foo = 'bar') OR (bar = 'baz')";
        $this->assertSame($expected, $delete->toString());
    }
}