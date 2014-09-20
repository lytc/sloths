<?php

namespace SlothsTest\Db\Migration;

use Sloths\Db\ConnectionManager;
use Sloths\Db\Migration\Migrator;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Db\Migration\Migrator
 */
class MigratorTest extends TestCase
{
    public function testList()
    {
        $directory = __DIR__ . '/fixtures/migrations';

        $stmt = $this->getMock('stmt', ['fetchAll']);
        $stmt->expects($this->atLeast(1))
            ->method('fetchAll')
            ->with(\PDO::FETCH_COLUMN, 0)
            ->willReturn(['20140901000000']);

        $pdo = $this->getMock('mockpdo', ['query']);
        $pdo->expects($this->exactly(2))
            ->method('query')
            ->with("SELECT `version` FROM `migrations`")
            ->willReturn($stmt);

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->atLeast(1))->method('getPdo')->willReturn($pdo);

        $connectionManager = new ConnectionManager();
        $connectionManager->setConnection($connection);

        $migrator = new Migrator();
        $migrator
            ->setDirectory($directory)
            ->setNamespace('MigrationStub\\')
            ->setConnectionManager($connectionManager)
        ;

        $expectedMigrated = [
            '20140901000000' => [
                'version' => '20140901000000',
                'className' => 'MigrationStub\Foo',
                'file' => $directory . '/20140901000000-Foo.php'
            ],
        ];


        $expectedPending = [
            '20140901000001' => [
                'version' => '20140901000001',
                'className' => 'MigrationStub\Bar',
                'file' => $directory . '/20140901000001-Bar.php'
            ]
        ];

        $allMigrationExpected = $expectedMigrated + $expectedPending;

        $this->assertSame($allMigrationExpected, $migrator->listMigrations());
        $this->assertSame($expectedMigrated, $migrator->listMigrated());
        $this->assertSame($expectedPending, $migrator->listPending());
    }

    public function testLastMigrated()
    {
        $directory = __DIR__ . '/fixtures/migrations';

        $stmt = $this->getMock('stmt', ['fetchColumn']);
        $stmt->expects($this->once())
            ->method('fetchColumn')
            ->willReturn('20140901000000');

        $pdo = $this->getMock('mockpdo', ['query']);
        $pdo->expects($this->exactly(1))
            ->method('query')
            ->with("SELECT `version` FROM `migrations` ORDER BY `version` DESC LIMIT 1")
            ->willReturn($stmt);

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->atLeast(1))->method('getPdo')->willReturn($pdo);

        $connectionManager = new ConnectionManager();
        $connectionManager->setConnection($connection);

        $migrator = new Migrator();
        $migrator
            ->setDirectory($directory)
            ->setNamespace('MigrationStub\\')
            ->setConnectionManager($connectionManager)
        ;

        $expected = [
            'version' => '20140901000000',
            'className' => 'MigrationStub\Foo',
            'file' => $directory . '/20140901000000-Foo.php'
        ];
        $this->assertSame($expected, $migrator->getLastMigrated());
    }

    public function testMigrate()
    {
        $directory = __DIR__ . '/fixtures/migrations';
        $migration = [
            'version' => '20140901000000',
            'className' => 'MigrationStub\Foo',
            'file' => $directory . '/20140901000000-Foo.php'
        ];

        $migrations = [
            '20140901000000' => $migration
        ];

        $pdo = $this->getMock('mockpdo', ['exec']);
        $pdo->expects($this->at(0))->method('exec')->with("foo up");
        $pdo->expects($this->at(1))->method('exec')->with("INSERT INTO `migrations` SET `version` = '20140901000000'");

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->atLeast(1))->method('getPdo')->willReturn($pdo);

        $connectionManager = new ConnectionManager();
        $connectionManager->setConnection($connection);

        $migrator = $this->getMock('Sloths\Db\Migration\Migrator', ['listPending', 'triggerEventListener']);
        $migrator->expects($this->once())->method('listPending')->willReturn($migrations);

        $migrator->expects($this->at(1))->method('triggerEventListener')->with('migrate', [$migration]);
        $migrator->expects($this->at(2))->method('triggerEventListener')->with('migrated', [$migration]);

        $migrator
            ->setDirectory($directory)
            ->setConnectionManager($connectionManager)
        ;

        $migrator->migrate();
    }

    public function testRollback()
    {
        $directory = __DIR__ . '/fixtures/migrations';
        $migration = [
            'version' => '20140901000000',
            'className' => 'MigrationStub\Foo',
            'file' => $directory . '/20140901000000-Foo.php'
        ];

        $pdo = $this->getMock('mockpdo', ['exec']);
        $pdo->expects($this->at(0))->method('exec')->with("foo down");
        $pdo->expects($this->at(1))->method('exec')->with("DELETE FROM `migrations` WHERE `version` = '20140901000000'");

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->atLeast(1))->method('getPdo')->willReturn($pdo);

        $connectionManager = new ConnectionManager();
        $connectionManager->setConnection($connection);

        $migrator = $this->getMock('Sloths\Db\Migration\Migrator', ['getLastMigrated', 'triggerEventListener']);
        $migrator->expects($this->once())->method('getLastMigrated')->willReturn($migration);

        $migrator->expects($this->at(1))->method('triggerEventListener')->with('rollback', [$migration]);
        $migrator->expects($this->at(2))->method('triggerEventListener')->with('rolledback', [$migration]);

        $migrator
            ->setDirectory($directory)
            ->setConnectionManager($connectionManager)
        ;

        $migrator->rollback();
    }
}