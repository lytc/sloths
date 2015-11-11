<?php

namespace SlothsTest\Db\Sql;

use Sloths\Db\Sql\Update;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Db\Sql\Update
 */
class UpdateTest extends TestCase
{
    public function test()
    {
        $update = new Update();
        $update
            ->table('users')
            ->values(['name' => 'name', 'email' => 'email'])
            ->where('id IN(?)', [1, 2])
            ->orderBy('id')
            ->limit(10)
        ;

        $expected = "UPDATE users SET `name` = 'name', `email` = 'email' WHERE (id IN (1, 2)) ORDER BY id LIMIT 10";
        $this->assertSame($expected, $update->toString());
    }
}