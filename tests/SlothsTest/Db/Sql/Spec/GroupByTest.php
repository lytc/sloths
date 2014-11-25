<?php

namespace SlothsTest\Db\Sql\Spec;

use Sloths\Db\Sql\Spec\GroupBy;
use SlothsTest\TestCase;

/**
 * @covers \Sloths\Db\Sql\Spec\GroupBy
 */
class GroupByTest extends TestCase
{
    public function test()
    {
        $groupBy = new GroupBy();
        $groupBy->add('foo')->add(['bar', 'baz']);

        $this->assertSame("GROUP BY foo, bar, baz", $groupBy->toString());

        $groupBy->reset();
        $this->assertSame('', $groupBy->toString());
    }
}