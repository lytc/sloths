<?php

namespace SlothsTest\Db\Sql;
use Sloths\Db\Sql\Replace;

class ReplaceTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $replace = new Replace('foo');
        $replace->values(['foo' => 'foo', 'bar' => 'bar']);
        $expected = "REPLACE INTO foo (foo, bar) VALUES ('foo', 'bar')";
        $this->assertSame($expected, $replace->toString());
    }
}