<?php

namespace SlothsTest\Db\Sql\Spec;

use Sloths\Db\Sql\Spec\Limit;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Db\Sql\Spec\Limit
 */
class LimitTest extends TestCase
{
    public function test()
    {
        $limit = new Limit();

        $limit->limit(10);
        $this->assertSame("LIMIT 10", $limit->toString());

        $limit->offset(20);
        $this->assertSame("LIMIT 10 OFFSET 20", $limit->toString());

        $limit->reset();
        $this->assertSame('', $limit->toString());

        $limit->limit(10, 20);
        $this->assertSame("LIMIT 10 OFFSET 20", $limit->toString());
    }
}