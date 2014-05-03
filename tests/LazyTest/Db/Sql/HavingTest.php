<?php

namespace LazyTest\Db\Sql;
use Lazy\Db\Db;
use Lazy\Db\Sql\Having;

class HavingTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleConditions()
    {
        $having = new Having();
        $having->having("foo = 'foo' AND bar = 'bar'")
            ->having("baz = 'baz'")
            ->orHaving("qux = 'qux'");

        $expected = "HAVING (foo = 'foo' AND bar = 'bar') AND (baz = 'baz') OR (qux = 'qux')";
        $this->assertSame($expected, $having->toString());
    }
}