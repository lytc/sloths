<?php

namespace SlothsTest\Db\Model;

use Sloths\Db\Model\Collection;
use SlothsTest\Db\Model\Stub\User;
use Sloths\Application\Service\Database;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Db\Model\Collection
 */
class CollectionTest extends TestCase
{
    public function testLoad()
    {
        $stmt = $this->getMock('stmt', ['fetchAll']);
        $stmt->expects($this->exactly(2))->method('fetchAll')->willReturn([]);

        $pdo = $this->getMock('mockpdo', ['query']);
        $pdo->expects($this->exactly(2))->method('query')
            ->with("SELECT users.id, users.username, users.password, users.created_time, users.modified_time FROM users")
            ->willReturn($stmt)
        ;

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->atLeast(1))->method('getPdo')->willReturn($pdo);

        $database = new Database();
        $database->setConnection($connection);

        User::setDatabase($database);

        $users = User::all();
        $users->load();
        $users->load();
        $users->load(true);
    }

    public function testReload()
    {
        $collection = $this->getMock('Sloths\Db\Model\Collection', ['load'], [], '', false);
        $collection->expects($this->once())->method('load')->with(true);
        $collection->reload();
    }

    public function testGetAtAndFirst()
    {
        $rows = [
            ['id' => 1],
            ['id' => 2],
        ];
        $stmt = $this->getMock('stmt', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')->willReturn($rows);

        $pdo = $this->getMock('mockpdo', ['query']);
        $pdo->expects($this->once())->method('query')
            ->with("SELECT users.id, users.username, users.password, users.created_time, users.modified_time FROM users")
            ->willReturn($stmt)
        ;

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->atLeast(1))->method('getPdo')->willReturn($pdo);

        $database = new Database();
        $database->setConnection($connection);

        User::setDatabase($database);

        $users = User::all();
        $this->assertSame($rows[0], $users->getAt(0)->toArray());
        $this->assertSame($rows[1], $users->getAt(1)->toArray());
        $this->assertSame($rows[0], $users->first()->toArray());
    }

    public function testColumn()
    {
        $collection = $this->getMock('Sloths\Db\Model\Collection', ['toArray'], [], '', false);
        $collection->expects($this->once())->method('toArray')->willReturn([['id' => 1, 'name' => 'foo'], ['id' => 2, 'name' => 'bar']]);
        $this->assertSame(['foo', 'bar'], $collection->column('name'));
    }

    public function testIds()
    {
        $collection = $this->getMock('Sloths\Db\Model\Collection', ['column'], [[], __NAMESPACE__ . '\Stub\User']);
        $collection->expects($this->once())->method('column')->with('id')->willReturn([1, 2]);
        $this->assertSame([1, 2], $collection->ids());
    }

    public function testToArrayAndCount()
    {
        $rows = [['id' => 1], ['id' => 2]];

        $collection = new Collection($rows, __NAMESPACE__ . '\Stub\User');
        $this->assertSame($rows, $collection->toArray());
        $this->assertSame(2, $collection->count());
    }

    public function testFoundRows()
    {
        $stmt = $this->getMock('stmt', ['fetchColumn']);
        $stmt->expects($this->once())->method('fetchColumn')->willReturn(10);

        $pdo = $this->getMock('mockpdo', ['query']);
        $pdo->expects($this->at(0))->method('query')->with("SELECT COUNT(*) FROM users")->willReturn($stmt);

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->once())->method('getPdo')->willReturn($pdo);

        $database = new Database();
        $database->setConnection($connection);

        User::setDatabase($database);
        $users = User::all()->select('*')->limit(10);

        $this->assertSame(10, $users->foundRows());
    }

    public function testSet()
    {
        $users = new Collection([['id' => 1, 'username' => 'foo'], ['id' => 2, 'username' => 'bar']], __NAMESPACE__ . '\Stub\User');
        $users->username = 'baz';

        $this->assertSame('baz', $users->getAt(0)->username);
        $this->assertSame('baz', $users->getAt(1)->username);
    }

    public function testSaveAndDelete()
    {
        $collection = $this->getMock('Sloths\Db\Model\Collection', ['callEach'], [], '', false);
        $collection->expects($this->at(0))->method('callEach')->with('save');
        $collection->expects($this->at(1))->method('callEach')->with('delete');

        $collection->save();
        $collection->delete();
    }
}