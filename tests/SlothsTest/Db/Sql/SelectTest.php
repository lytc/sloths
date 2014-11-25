<?php

namespace SlothsTest\Db\Sql;

use Sloths\Db\Sql\Select;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Db\Sql\Select
 */
class SelectTest extends TestCase
{
    public function test()
    {
        $select = new Select();

        $select->table('users u');
        $this->assertSame("SELECT u.* FROM users AS u", $select->toString());

        $select
            ->select('id, name, email')
            ->where('name LIKE %?%', 'lytc')
            ->orderBy('name, id DESC')
            ->limit(10, 20)
        ;

        $expected = "SELECT id, name, email FROM users AS u WHERE (name LIKE '%lytc%') ORDER BY name, id DESC LIMIT 10 OFFSET 20";
        $this->assertSame($expected, $select->toString());
    }
}