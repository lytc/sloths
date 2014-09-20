<?php

namespace SlothsTest\Db\Table\Sql;

use Sloths\Db\Table\Sql\Insert;
use SlothsTest\TestCase;

class InsertTest extends TestCase
{
    public function testRun()
    {
        $connection = $this->getMock('Sloths\Db\Connection', ['getLastInsertId'], ['dsn']);
        $connection->expects($this->once())->method('getLastInsertId')->willReturn(10);

        $insert = $this->getMock('Sloths\Db\Table\Sql\Insert', ['traitRun']);
        $insert->expects($this->exactly(2))->method('traitRun')->willReturn(1);
        $insert->setConnection($connection);

        $this->assertSame(1, $insert->run(false));
        $this->assertSame(10, $insert->run());
    }
}