<?php

namespace LazyTest\Db\Sql;

use Lazy\Db\Sql\Limit;

/**
 * @covers Lazy\Db\Sql\Limit
 */
class LimitTest extends \PHPUnit_Framework_TestCase
{

    public function testGetSetLimit()
    {
        $limit = new Limit(10);
        $this->assertSame('LIMIT 10', $limit->toString());
        $this->assertSame(10, $limit->limit());

        $limit = new Limit();
        $limit->limit(10);
        $this->assertSame('LIMIT 10', $limit->toString());
        $this->assertSame(10, $limit->limit());
    }

    public function testLimitShouldReturnAnEmptyStringWhenLimitNotSet()
    {
        $limit = new Limit();
        $this->assertSame('', $limit->toString());
        $this->assertNull($limit->limit());
    }

    public function testReset()
    {
        $limit = new Limit(10);
        $this->assertSame(10, $limit->limit());
        $limit->reset();
        $this->assertNull($limit->limit());
    }
}