<?php

namespace SlothsTest\Db\Migration;

use Sloths\Application\Service\ConnectionManager;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Db\Migration\AbstractMigration
 */
class AbstractMigrationTest extends TestCase
{
    public function test()
    {
        $pdo = $this->getMock('mockpdo', ['exec', 'query']);
        $pdo->expects($this->once())->method('exec')->with('foo')->willReturn('foo');
        $pdo->expects($this->once())->method('query')->with('bar')->willReturn('bar');

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->atLeast(1))->method('getPdo')->willReturn($pdo);

        $migration = $this->getMockForAbstractClass('Sloths\Db\Migration\AbstractMigration');
        $migration->setConnection($connection);

        $this->assertSame('foo', $migration->exec('foo'));
        $this->assertSame('bar', $migration->query('bar'));
    }
}