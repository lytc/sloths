<?php

namespace SlothsTest\Db\Model\Relation;

use MockModel\User;
use Sloths\Db\ConnectionManager;
use Sloths\Db\Model\Collection;

class HasManyThroughTest extends TestCase
{
    public function test()
    {
        $rows = [
            ['id' => 2, 'name' => 'foo'],
            ['id' => 3, 'name' => 'bar'],
        ];

        $stmt = $this->getMock('stmt', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($rows);

        $connection = $this->getMock('Sloths\Db\Connection', ['query'], ['dsn']);
        $connection->expects($this->once())->method('query')
            ->with("SELECT roles.id, roles.name, user_roles.user_id FROM roles INNER JOIN user_roles ON ((user_roles.role_id = roles.id) AND (user_roles.user_id = 1))")
            ->willReturn($stmt);

        $connectionManager = new ConnectionManager();
        $connectionManager->setConnection($connection);


        $user = new User(['id' => 1]);
        $user->setDefaultConnectionManager($connectionManager);

        $roles = $user->getHasMany('Roles');
        $this->assertSame($rows, $roles->toArray());
    }

    public function testWithParentCollection()
    {
        $userRows = [
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
        ];

        $roleRows = [
            ['id' => 1, 'name' => 'foo', 'user_id' => 1],
            ['id' => 2, 'name' => 'bar', 'user_id' => 2],
            ['id' => 3, 'name' => 'baz', 'user_id' => 1],
        ];

        $users = new Collection($userRows, new User());
        $user1 = $users->getAt(0);
        $user2 = $users->getAt(1);
        $user3 = $users->getAt(2);

        $stmt = $this->getMock('stmt', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($roleRows);

        $connection = $this->getMock('Sloths\Db\Connection', ['query'], ['dsn']);
        $connection->expects($this->once())->method('query')
            ->with("SELECT roles.id, roles.name, user_roles.user_id FROM roles INNER JOIN user_roles ON ((user_roles.role_id = roles.id) AND (user_roles.user_id IN (1, 2, 3)))")
            ->willReturn($stmt);

        $connectionManager = new ConnectionManager();
        $connectionManager->setConnection($connection);


        $user1->setDefaultConnectionManager($connectionManager);

        $roles = $user1->getHasMany('Roles');

        $expected = [['id' => 1, 'name' => 'foo', 'user_id' => 1], ['id' => 3, 'name' => 'baz', 'user_id' => 1]];
        $this->assertSame($expected, $roles->toArray());

        $expected = [['id' => 2, 'name' => 'bar', 'user_id' => 2]];
        $this->assertSame($expected, $user2->getRelation('Roles', true)->toArray());

        $this->assertSame([], $user3->getRelation('Roles', true)->toArray());

    }
}