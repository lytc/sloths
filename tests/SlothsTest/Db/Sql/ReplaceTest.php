<?php

namespace SlothsTest\Db\Sql;
use Sloths\Db\Sql\Replace;
use Sloths\Db\Sql\Select;

/**
 * @covers \Sloths\Db\Sql\Replace
 */
class ReplaceTest extends \PHPUnit_Framework_TestCase
{
    public function testValues()
    {
        $replace = new Replace('foo');
        $replace->values(['foo' => 'foo', 'bar' => 'bar']);
        $expected = "REPLACE INTO foo (foo, bar) VALUES ('foo', 'bar')";
        $this->assertSame($expected, $replace->toString());
    }

    public function testColumns()
    {
        $replace = new Replace('foo');
        $replace->columns('foo, bar')->values(['foo', 'bar']);

        $expected = "REPLACE INTO foo (foo, bar) VALUES ('foo', 'bar')";
        $this->assertSame($expected, $replace->toString());
    }

    public function testColumnAutoGetFromValuesKey()
    {
        $replace = new Replace('foo');
        $replace->values(['foo' => 'foo', 'bar' => 'bar']);

        $expected = "REPLACE INTO foo (foo, bar) VALUES ('foo', 'bar')";
        $this->assertSame($expected, $replace->toString());
    }

    public function testMultipleValues()
    {
        $replace = new Replace('foo');
        $replace->values([['foo' => 'foo'], ['foo' => 'bar']]);
        $expected = "REPLACE INTO foo (foo) VALUES ('foo'), ('bar')";
        $this->assertSame($expected, $replace->toString());
    }

    public function testLowPriorityAndDelayedOptions()
    {
        $replace = new Replace('foo');

        $replace->lowPriority()->values(['foo' => 'foo']);
        $this->assertSame("REPLACE LOW_PRIORITY INTO foo (foo) VALUES ('foo')", $replace->toString());

        $replace->delayed();
        $this->assertSame("REPLACE DELAYED INTO foo (foo) VALUES ('foo')", $replace->toString());

        $replace->delayed(false);
        $this->assertSame("REPLACE INTO foo (foo) VALUES ('foo')", $replace->toString());
    }

    public function testSelect()
    {
        $replace = new Replace('foo');
        $replace->select((new Select('bar'))->select('foo'));

        $this->assertSame("REPLACE INTO foo SELECT foo FROM bar", $replace->toString());
    }
}