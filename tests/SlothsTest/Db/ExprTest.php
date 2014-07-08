<?php

namespace SlothsTest\Db;

use Sloths\Db\Expr;

class ExprTest extends TestCase
{
    public function test()
    {
        $expr = new Expr(1);
        $this->assertSame(1, $expr->getValue());
        $this->assertSame('1', (string) $expr);
    }
}