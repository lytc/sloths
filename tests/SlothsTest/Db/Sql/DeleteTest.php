<?php

namespace SlothsTest\Db\Sql;

use Sloths\Db\Sql\Delete;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Db\Sql\Delete
 */
class DeleteTest extends TestCase
{
    public function test()
    {
        $delete = new Delete();
        $delete
            ->table('users')
            ->where('id IN(?)', [1, 2])
            ->orderBy('id')
            ->limit(10)
        ;

        $expected = "DELETE FROM users WHERE (id IN (1, 2)) ORDER BY id LIMIT 10";
        $this->assertSame($expected, $delete->toString());
    }
}