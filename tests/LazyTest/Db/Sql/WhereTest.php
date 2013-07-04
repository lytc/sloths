<?php

namespace LazyTest\Db\Sql;

use Lazy\Db\Connection;
use Lazy\Db\Expr;
use Lazy\Db\Sql\Where;
use LazyTest\Db\TestCase;

/**
 * @covers Lazy\Db\Sql\Where<extended>
 */
class WhereTest extends TestCase
{
    public function testNothingReturnsAnEmptyString()
    {
        $where = new Where($this->connection);
        $this->assertEquals('', $where->toString());
    }

    public function testWithParamString()
    {
        $where = new Where($this->connection);
        $where->where("foo = 'bar' AND bar = 'baz'");
        $expected = "WHERE (foo = 'bar' AND bar = 'baz')";
        $this->assertEquals($expected, $where->toString());
    }

    public function testWithParamStringAndBindQuestionMarkParams()
    {
        $where = new Where($this->connection);
        $where->where("foo = ? AND bar = ?", array('foo', 'bar'));
        $expected = "WHERE (foo = 'foo' AND bar = 'bar')";
        $this->assertEquals($expected, $where->toString());
    }

    public function testWithParamStringAndBindNamedParams()
    {
        $where = new Where($this->connection);
        $where->where("foo = :foo AND bar = :bar", array('bar' => 'bar', 'foo' => 'foo'));
        $expected = "WHERE (foo = 'foo' AND bar = 'bar')";
        $this->assertEquals($expected, $where->toString());
    }

    public function testWithParamStringAndBindBothQuestionMarkAndNamedParams()
    {
        $where = new Where($this->connection);
        $where->where("foo = :foo AND bar = ? AND baz = :baz", array('baz' => 'baz', 'bar', 'foo' => 'foo'));
        $expected = "WHERE (foo = 'foo' AND bar = 'bar' AND baz = 'baz')";
        $this->assertEquals($expected, $where->toString());
    }

    public function testMultipleWhereWithStringParam()
    {
        $where = new Where($this->connection);
        $where->where('foo = ? AND bar = :bar', array('foo', 'bar' => 'bar'));
        $where->where('baz = :baz AND qux = ?', array('baz' => 'baz', 'qux'));
        $expected = "WHERE (foo = 'foo' AND bar = 'bar') AND (baz = 'baz' AND qux = 'qux')";
        $this->assertEquals($expected, $where->toString());
    }

    public function testWithParamArray()
    {
        $where = new Where($this->connection);
        $where->where(array(
            'foo' => 'foo',
            "bar = 'bar' OR baz = ?" => 'baz',
            'qux = 1'
        ));

        $expected = "WHERE (foo = 'foo' AND bar = 'bar' OR baz = 'baz' AND qux = 1)";
        $this->assertEquals($expected, $where->toString());
    }

    public function testParamArrayAndHasConditionType()
    {
        $where = new Where($this->connection);
        $where->where(array(
            'foo' => 'foo',
            "OR bar = 'bar' OR baz = ?" => 'baz',
            'or qux = 1'
        ));

        $expected = "WHERE (foo = 'foo' OR bar = 'bar' OR baz = 'baz' or qux = 1)";
        $this->assertEquals($expected, $where->toString());
    }

    public function testOrWhere()
    {
        $where = new Where($this->connection);
        $where->where("foo = ?", array('foo'));
        $where->orWhere('bar = :bar', array('bar' => 'bar'));
        $where->where('baz = 1');
        $expected = "WHERE (foo = 'foo') OR (bar = 'bar') AND (baz = 1)";
        $this->assertEquals($expected, $where->toString());

        $where = new Where($this->connection);
        $where->where('foo = ?', array('foo'));
        $where->orWhere("bar = ? AND baz = ?", 'bar', 'baz');
        $expected = "WHERE (foo = 'foo') OR (bar = 'bar' AND baz = 'baz')";
        $this->assertEquals($expected, $where->toString());
    }

    public function testAutoConvertBindParamToArrayIfItIsNotAnArray()
    {
        $where = new Where($this->connection);
        $where->where('foo = ?', 'foo');
        $this->assertEquals("WHERE (foo = 'foo')", $where->toString());
    }

    public function testMultipleBindParams()
    {
        $where = new Where($this->connection);
        $where->where('foo = ? AND bar = ?', 'foo', 'bar');
        $this->assertEquals("WHERE (foo = 'foo' AND bar = 'bar')", $where->toString());
    }

    public function testBindParamShouldNotEscapeIfValueIsExpr()
    {
        $where = new Where($this->connection);
        $where->where('foo = ?', new Expr("'foo'"));
        $this->assertEquals("WHERE (foo = 'foo')", $where->toString());
    }

    public function testBindInWithQuestionMark()
    {
        $where = new Where($this->connection);
        $where->where('foo IN(?)', array(1, 2));
        $this->assertEquals("WHERE (foo IN(1, 2))", $where->toString());

        $where = new Where($this->connection);
        $where->where('foo IN(?)', array(array(1, 2)));
        $this->assertEquals("WHERE (foo IN(1, 2))", $where->toString());
    }

    public function testBindInWithNamed()
    {
        $where = new Where($this->connection);
        $where->where('foo IN(:foo)', array('foo' => array(1, 2)));
        $this->assertEquals("WHERE (foo IN(1, 2))", $where->toString());
    }

    public function testLike()
    {
        $where = new Where($this->connection);
        $where->where("foo LIKE '%?%'", new Expr($this->connection->escape("foo'bar")));
        $this->assertEquals("WHERE (foo LIKE '%foo\'bar%')", $where->toString());
    }

    public function testSmartBinding()
    {
        $where = new Where($this->connection);
        $where->where(array(
            'foo' => 'foo',
            'bar' => null
        ));

        $expected = "WHERE (foo = 'foo' AND bar IS NULL)";
        $this->assertEquals($expected, $where->toString());
    }

    public function testReset()
    {
        $where = new Where($this->connection);
        $where->where("foo = 'bar' AND bar = 'baz'");
        $expected = "WHERE (foo = 'bar' AND bar = 'baz')";
        $this->assertEquals($expected, $where->toString());

        $where->reset();
        $this->assertSame('', $where->toString());
    }
}