<?php

namespace SlothsTest\Db\Sql\Spec;

use Sloths\Db\Sql\Spec\OrderBy;
use SlothsTest\TestCase;

/**
 * @covers \Sloths\Db\Sql\Spec\OrderBy
 */
class OrderByTest extends TestCase
{
    public function test()
    {
        $orderBy = new OrderBy();
        $this->assertSame('', $orderBy->toString());

        $orderBy->add('foo');
        $this->assertSame('ORDER BY foo', $orderBy->toString());

        $orderBy->add('bar ASC');
        $this->assertSame('ORDER BY foo, bar ASC', $orderBy->toString());

        $orderBy->add(['baz', 'qux' => 'DESC']);
        $this->assertSame('ORDER BY foo, bar ASC, baz, qux DESC', $orderBy->toString());

        $orderBy->reset();
        $this->assertSame('', $orderBy->toString());
    }
}