<?php

namespace LazyTest\Db\Sql;
use Lazy\Db\Sql\Select;

class SelectTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $select = new Select('foo');
        $this->assertSame("SELECT foo.* FROM foo", $select->toString());

        $select = new Select('foo f');
        $this->assertSame("SELECT f.* FROM foo f", $select->toString());
    }

    public function testSelectColumn()
    {
        $select = new Select();
        $select->from('foo')
            ->select('foo f, bar b, baz');

        $this->assertSame("SELECT foo f, bar b, baz FROM foo", $select->toString());
    }

    public function testSelectColumnArray()
    {
        $select = new Select();
        $select->from('foo')
            ->select(['f' => 'foo', 'b' => 'bar', 'baz']);

        $this->assertSame("SELECT foo f, bar b, baz FROM foo", $select->toString());
    }

    public function testSubSelect()
    {
        $sub = new Select('bar');
        $sub->select('id')->where('bar.foo_id = foo.id');

        $select = new Select();
        $select->from('foo')
            ->select(['f' => 'foo', 'b' => $sub]);

        $this->assertSame("SELECT foo f, (SELECT id FROM bar WHERE (bar.foo_id = foo.id)) b FROM foo", $select->toString());
    }

    public function testOrderBy()
    {
        $select = new Select('foo');
        $select->orderBy('bar, baz DESC');

        $this->assertSame("SELECT foo.* FROM foo ORDER BY bar, baz DESC", $select->toString());

        $select = new Select('foo');
        $select->orderBy(['bar', 'baz DESC']);

        $this->assertSame("SELECT foo.* FROM foo ORDER BY bar, baz DESC", $select->toString());
    }

    public function testGroupBy()
    {
        $select = new Select('foo');
        $select->groupBy('bar, baz');

        $this->assertSame("SELECT foo.* FROM foo GROUP BY bar, baz", $select->toString());

        $select = new Select('foo');
        $select->groupBy(['bar', 'baz']);

        $this->assertSame("SELECT foo.* FROM foo GROUP BY bar, baz", $select->toString());
    }

    public function testLimit()
    {
        $select = new Select('foo');
        $select->limit(10);
        $this->assertSame("SELECT foo.* FROM foo LIMIT 10", $select->toString());

        $select = new Select('foo');
        $select->limit(10, 3);
        $this->assertSame("SELECT foo.* FROM foo LIMIT 10 OFFSET 3", $select->toString());

        $select = new Select('foo');
        $select->limit(10)->offset(3);
        $this->assertSame("SELECT foo.* FROM foo LIMIT 10 OFFSET 3", $select->toString());
    }

    public function testOptions()
    {
        $select = new Select('foo');
        $select->distinct()->calcFoundRows();
        $this->assertSame("SELECT DISTINCT SQL_CALC_FOUND_ROWS foo.* FROM foo", $select->toString());
    }

    public function testJoin()
    {
        $select = new Select('foo');
        $select->join('bar');

        $expected = "SELECT foo.* FROM foo INNER JOIN bar ON bar.foo_id = foo.id";
        $this->assertSame($expected, $select->toString());

        $select = new Select('foo');
        $select->join('bar', 'bar.foo_id = foo.id');
        $this->assertSame($expected, $select->toString());

        $select = new Select('foo f');
        $select->join('bar b');
        $expected = "SELECT f.* FROM foo f INNER JOIN bar b ON b.foo_id = f.id";
        $this->assertSame($expected, $select->toString());
    }

    public function testLeftJoin()
    {
        $select = new Select('foo');
        $select->leftJoin('bar');

        $expected = "SELECT foo.* FROM foo LEFT JOIN bar ON bar.foo_id = foo.id";
        $this->assertSame($expected, $select->toString());

        $select = new Select('foo');
        $select->leftJoin('bar', 'bar.foo_id = foo.id');
        $this->assertSame($expected, $select->toString());

        $select = new Select('foo f');
        $select->leftJoin('bar b');
        $expected = "SELECT f.* FROM foo f LEFT JOIN bar b ON b.foo_id = f.id";
        $this->assertSame($expected, $select->toString());
    }

    public function testRightJoin()
    {
        $select = new Select('foo');
        $select->rightJoin('bar');

        $expected = "SELECT foo.* FROM foo RIGHT JOIN bar ON bar.foo_id = foo.id";
        $this->assertSame($expected, $select->toString());

        $select = new Select('foo');
        $select->rightJoin('bar', 'bar.foo_id = foo.id');
        $this->assertSame($expected, $select->toString());

        $select = new Select('foo f');
        $select->rightJoin('bar b');
        $expected = "SELECT f.* FROM foo f RIGHT JOIN bar b ON b.foo_id = f.id";
        $this->assertSame($expected, $select->toString());
    }

    public function testWhere()
    {
        $select = new Select('foo');
        $this->assertInstanceOf('Lazy\Db\Sql\Where', $select->where());
        $this->assertInstanceOf('Lazy\Db\Sql\Where', $select->orWhere());

        $select->where("foo = 'bar'")->orWhere("bar = 'baz'");
        $expected = "SELECT foo.* FROM foo WHERE (foo = 'bar') OR (bar = 'baz')";
        $this->assertSame($expected, $select->toString());
    }

    public function testHaving()
    {
        $select = new Select('foo');
        $this->assertInstanceOf('Lazy\Db\Sql\Having', $select->having());
        $this->assertInstanceOf('Lazy\Db\Sql\Having', $select->orHaving());

        $select->having("foo = 'bar'")->orHaving("bar = 'baz'");
        $expected = "SELECT foo.* FROM foo HAVING (foo = 'bar') OR (bar = 'baz')";
        $this->assertSame($expected, $select->toString());
    }
}