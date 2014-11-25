<?php

namespace SlothsTest\Db\Table\Sql;

use Sloths\Db\Table\Sql\SqlWriteTrait;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Db\Table\Sql\SqlWriteTrait
 */
class SqlWriteTraitTest extends TestCase
{
    public function test()
    {
        $connection = $this->getMock('connection', ['exec']);
        $connection->expects($this->once())->method('exec')->with('sql string')->willReturn('result');

        $sql = $this->getMock(__NAMESPACE__ . '\SqlWrite', ['getConnection', 'toString']);
        $sql->expects($this->once())->method('toString')->willReturn('sql string');
        $sql->expects($this->once())->method('getConnection')->willReturn($connection);

        $this->assertSame('result', $sql->run());
    }
}

class SqlWrite
{
    use SqlWriteTrait;
}