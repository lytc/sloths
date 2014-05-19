<?php

namespace SlothsTest\Db\Sql;
use Sloths\Db\Db;
use Sloths\Db\Sql\Where;

class WhereTest extends \PHPUnit_Framework_TestCase
{
    public function testItShouldReturnsAnEmptyStringWithNoCondition()
    {
        $where = new Where();
        $this->assertSame('', $where->toString());
    }

    public function testSimpleConditions()
    {
        $where = new Where();
        $where->where("foo = 'foo' AND bar = 'bar'")
            ->where("baz = 'baz'")
            ->orWhere("qux = 'qux'");

        $expected = "WHERE (foo = 'foo' AND bar = 'bar') AND (baz = 'baz') OR (qux = 'qux')";
        $this->assertSame($expected, $where->toString());
    }

    public function testWithBindParamsArray()
    {
        $where = new Where();
        $where->where("foo = ? AND bar = ?", ['foo', 'bar'])
            ->where("baz = ?", ['baz'])
            ->orWhere('qux = ?', ['qux']);

        $expected = "WHERE (foo = 'foo' AND bar = 'bar') AND (baz = 'baz') OR (qux = 'qux')";
        $this->assertSame($expected, $where->toString());
    }

    public function testWithBindParamsArrayMissingValue()
    {
        $where = new Where();
        $where->where("foo = ? AND bar = ?", ['foo'])
            ->where("baz = ?", ['baz'])
            ->orWhere('qux = ?', ['qux']);

        $expected = "WHERE (foo = 'foo' AND bar = 'foo') AND (baz = 'baz') OR (qux = 'qux')";
        $this->assertSame($expected, $where->toString());
    }

    public function testWithBindParamArguments()
    {
        $where = new Where();
        $where->where("foo = ? AND bar = ?", 'foo', 'bar')
            ->where("baz = ?", 'baz')
            ->orWhere('qux = ?', 'qux');

        $expected = "WHERE (foo = 'foo' AND bar = 'bar') AND (baz = 'baz') OR (qux = 'qux')";
        $this->assertSame($expected, $where->toString());
    }

    public function testSubWhere()
    {
        $where = new Where();
        $where->where("foo = ?", 'foo')
            ->where(function() {
                $this->where('bar = ? AND baz = ?', ['bar', 'baz']);
            })
            ->orWhere(function() {
                $this->where('qux = ? OR quux = ?', 'qux', 'quux');
            });

        $expected = "WHERE (foo = 'foo') AND ((bar = 'bar' AND baz = 'baz')) OR ((qux = 'qux' OR quux = 'quux'))";
        $this->assertSame($expected, $where->toString());
    }

    public function testNestedSubWhere()
    {
        $where = new Where();
        $where->where("foo = ?", 'foo')
            ->where(function() {
                $this->where('bar = ? AND baz = ?', ['bar', 'baz'])
                    ->orWhere(function() {
                        $this->where('qux = ? OR quux = ?', 'qux');
                    });
            });

        $expected = "WHERE (foo = 'foo') AND ((bar = 'bar' AND baz = 'baz') OR ((qux = 'qux' OR quux = 'qux')))";
        $this->assertSame($expected, $where->toString());
    }

    public function testEscape()
    {
        $where = new Where();
        $where->where("foo = ? AND bar = ?", ['f"oo', 'b"ar'])
            ->where("baz = ?", ['b"az'])
            ->orWhere('qux = ?', ['q"ux']);

        $expected = "WHERE (foo = 'f\\\"oo' AND bar = 'b\\\"ar') AND (baz = 'b\\\"az') OR (qux = 'q\\\"ux')";
        $this->assertSame($expected, $where->toString());
    }

    public function testItShouldNotEscapeWithExpr()
    {
        $where = new Where();
        $where->where("foo IN (?)", Db::expr("'\\\"foo', 'bar'"));

        $expected = "WHERE (foo IN ('\\\"foo', 'bar'))";
        $this->assertSame($expected, $where->toString());
    }

    public function testWhereFromArray()
    {
        $where = new Where();
        $where->where(['foo' => 'bar', 'bar' => 'baz']);
        $expected = "WHERE (foo = 'bar' AND bar = 'baz')";

        $this->assertSame($expected, $where->toString());
    }

    public function testSmartBinding()
    {
        $where = new Where();
        $where->where('foo', 'bar')
            ->where('bar', null)
            ->where('baz != ?', null)
            ->where('baz IN(?)', [1, true, 'foo'])
            ->where('qux LIKE %?%', 'q"ux');

        $expected = "WHERE (foo = 'bar') AND (bar IS NULL) AND (baz IS NOT NULL) AND (baz IN(1, 1, 'foo')) AND (qux LIKE '%q\\\"ux%')";
        $this->assertSame($expected, $where->toString());
    }
}