<?php

namespace SlothsTest\Pagination\DataAdapter;

use Sloths\Db\Sql\Select;
use Sloths\Pagination\DataAdapter\DbSelect;
use SlothsTest\TestCase;

class DbSelectTest extends \SlothsTest\Db\TestCase
{
    public function test()
    {
        $select = new Select('foo');

        $connection = $this->mockConnection();
        $connection->shouldReceive('selectAllWithFoundRows')->with($select)->andReturn([
            'foundRows' => 100,
            'rows' => []
        ]);

        $adapter = new DbSelect($select, $connection);
        $this->assertSame([], $adapter->items(10, 20));
        $this->assertSame(100, $adapter->count());

        $this->assertSame("SELECT SQL_CALC_FOUND_ROWS foo.* FROM foo LIMIT 10 OFFSET 20", $select->toString());
    }
}