<?php

namespace LazyTest\Db\Sql;

use Lazy\Db\Connection;
use Lazy\Db\Expr;
use Lazy\Db\Sql\Having;
use LazyTest\Db\TestCase;

/**
 * @covers Lazy\Db\Sql\Having<extended>
 */
class HavingTest extends TestCase
{
    public function testNothingReturnsAnEmptyString()
    {
        $having = new Having($this->connection);
        $this->assertEquals('', $having->toString());
    }

    public function testWithParamString()
    {
        $having = new Having($this->connection);
        $having->having("foo = 'bar' AND bar = 'baz'");
        $expected = "HAVING (foo = 'bar' AND bar = 'baz')";
        $this->assertEquals($expected, $having->toString());
    }

    public function testWithParamStringAndBindQuestionMarkParams()
    {
        $having = new Having($this->connection);
        $having->having("foo = ? AND bar = ?", array('foo', 'bar'));
        $expected = "HAVING (foo = 'foo' AND bar = 'bar')";
        $this->assertEquals($expected, $having->toString());
    }

    public function testWithParamStringAndBindNamedParams()
    {
        $having = new Having($this->connection);
        $having->having("foo = :foo AND bar = :bar", array('bar' => 'bar', 'foo' => 'foo'));
        $expected = "HAVING (foo = 'foo' AND bar = 'bar')";
        $this->assertEquals($expected, $having->toString());
    }

    public function testWithParamStringAndBindBothQuestionMarkAndNamedParams()
    {
        $having = new Having($this->connection);
        $having->having("foo = :foo AND bar = ? AND baz = :baz", array('baz' => 'baz', 'bar', 'foo' => 'foo'));
        $expected = "HAVING (foo = 'foo' AND bar = 'bar' AND baz = 'baz')";
        $this->assertEquals($expected, $having->toString());
    }

    public function testMultipleHavingWithStringParam()
    {
        $having = new Having($this->connection);
        $having->having('foo = ? AND bar = :bar', array('foo', 'bar' => 'bar'));
        $having->having('baz = :baz AND qux = ?', array('baz' => 'baz', 'qux'));
        $expected = "HAVING (foo = 'foo' AND bar = 'bar') AND (baz = 'baz' AND qux = 'qux')";
        $this->assertEquals($expected, $having->toString());
    }

    public function testWithParamArray()
    {
        $having = new Having($this->connection);
        $having->having(array(
            'foo' => 'foo',
            "bar = 'bar' OR baz = ?" => 'baz',
            'qux = 1'
        ));

        $expected = "HAVING (foo = 'foo' AND bar = 'bar' OR baz = 'baz' AND qux = 1)";
        $this->assertEquals($expected, $having->toString());
    }

    public function testParamArrayAndHasConditionType()
    {
        $having = new Having($this->connection);
        $having->having(array(
            'foo' => 'foo',
            "OR bar = 'bar' OR baz = ?" => 'baz',
            'or qux = 1'
        ));

        $expected = "HAVING (foo = 'foo' OR bar = 'bar' OR baz = 'baz' or qux = 1)";
        $this->assertEquals($expected, $having->toString());
    }

    public function testOrHaving()
    {
        $having = new Having($this->connection);
        $having->having("foo = ?", array('foo'));
        $having->orHaving('bar = :bar', array('bar' => 'bar'));
        $having->having('baz = 1');
        $expected = "HAVING (foo = 'foo') OR (bar = 'bar') AND (baz = 1)";
        $this->assertEquals($expected, $having->toString());

        $having = new Having($this->connection);
        $having->having('foo = ?', array('foo'));
        $having->orHaving("bar = ? AND baz = ?", 'bar', 'baz');
        $expected = "HAVING (foo = 'foo') OR (bar = 'bar' AND baz = 'baz')";
        $this->assertEquals($expected, $having->toString());
    }

    public function testAutoConvertBindParamToArrayIfItIsNotAnArray()
    {
        $having = new Having($this->connection);
        $having->having('foo = ?', 'foo');
        $this->assertEquals("HAVING (foo = 'foo')", $having->toString());
    }

    public function testMultipleBindParams()
    {
        $having = new Having($this->connection);
        $having->having('foo = ? AND bar = ?', 'foo', 'bar');
        $this->assertEquals("HAVING (foo = 'foo' AND bar = 'bar')", $having->toString());
    }

    public function testBindParamShouldNotEscapeIfValueIsExpr()
    {
        $having = new Having($this->connection);
        $having->having('foo = ?', new Expr("'foo'"));
        $this->assertEquals("HAVING (foo = 'foo')", $having->toString());
    }

    public function testBindInWithQuestionMark()
    {
        $having = new Having($this->connection);
        $having->having('foo IN(?)', array(1, 2));
        $this->assertEquals("HAVING (foo IN(1, 2))", $having->toString());

        $having = new Having($this->connection);
        $having->having('foo IN(?)', array(array(1, 2)));
        $this->assertEquals("HAVING (foo IN(1, 2))", $having->toString());
    }

    public function testBindInWithNamed()
    {
        $having = new Having($this->connection);
        $having->having('foo IN(:foo)', array('foo' => array(1, 2)));
        $this->assertEquals("HAVING (foo IN(1, 2))", $having->toString());
    }

    public function testLike()
    {
        $having = new Having($this->connection);
        $having->having("foo LIKE '%?%'", new Expr($this->connection->escape("foo'bar")));
        $this->assertEquals("HAVING (foo LIKE '%foo\'bar%')", $having->toString());
    }

    public function testReset()
    {
        $having = new Having($this->connection);
        $having->having("foo = 'bar' AND bar = 'baz'");
        $expected = "HAVING (foo = 'bar' AND bar = 'baz')";
        $this->assertEquals($expected, $having->toString());

        $having->reset();
        $this->assertSame('', $having->toString());
    }
}