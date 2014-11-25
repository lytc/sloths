<?php

namespace SlothsTest\Db\Sql\Spec;

use Sloths\Db\Sql\Spec\Limit;
use Sloths\Db\Sql\Spec\Raw;
use SlothsTest\TestCase;

/**
 * @covers \Sloths\Db\Sql\Spec\Raw
 */
class RawTest extends TestCase
{
    public function test()
    {
        $raw = new Raw('foo');
        $this->assertSame('foo', $raw->toString());
        $this->assertSame('foo', (string) $raw);
    }
}