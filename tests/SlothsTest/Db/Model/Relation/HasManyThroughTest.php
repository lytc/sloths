<?php

namespace SlothsTest\Db\Model\Relation;

use SlothsTest\Db\Model\Stub\User;
use SlothsTest\Db\Model\TestCase;

/**
 * @covers \Sloths\Db\Model\Model
 * @covers \Sloths\Db\Model\Relation\HasManyThrough
 * @covers \Sloths\Db\Model\ArrayCollection
 */
class HasManyThroughTest extends TestCase
{
    public function testWithCache()
    {
        $rows = [
            ['id' => 1, 'name' => 'foo'],
            ['id' => 2, 'name' => 'bar'],
        ];

        $stmt = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($rows);

        $pdo = $this->mockPdo('query');
        $pdo->expects($this->once())->method('query')
            ->with("SELECT roles.id, roles.name FROM roles INNER JOIN user_roles ON user_roles.role_id = roles.id WHERE (user_roles.user_id = 1)")
            ->willReturn($stmt);

        $connection = $this->createConnection($pdo);

        User::setConnection($connection);

        $user = new User(['id' => 1]);
        $roles = $user->Roles;
        $this->assertInstanceOf('Sloths\Db\Model\Relation\HasManyThrough', $roles);
        $this->assertCount(2, $roles);
        $this->assertSame($rows, $roles->toArray());
    }

    public function testNoCache()
    {
        $rows = [
            ['id' => 1, 'name' => 'foo'],
            ['id' => 2, 'name' => 'bar'],
        ];

        $stmt = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt->expects($this->exactly(3))->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($rows);

        $pdo = $this->mockPdo('query');
        $pdo->expects($this->exactly(3))->method('query')
            ->with("SELECT roles.id, roles.name FROM roles INNER JOIN user_roles ON user_roles.role_id = roles.id WHERE (user_roles.user_id = 1)")
            ->willReturn($stmt);

        $connection = $this->createConnection($pdo);

        User::setConnection($connection);

        $user = new User(['id' => 1]);

        $roles = $user->Roles;
        $this->assertInstanceOf('Sloths\Db\Model\Relation\HasManyThrough', $roles);
        $this->assertCount(2, $roles);
        $this->assertSame($rows, $roles->toArray());

        $roles2 = $user->getRelation('Roles');
        $this->assertInstanceOf('Sloths\Db\Model\Relation\HasManyThrough', $roles2);
        $this->assertCount(2, $roles2);
        $this->assertSame($rows, $roles2->toArray());

        $roles3 = $user->Roles();
        $this->assertInstanceOf('Sloths\Db\Model\Relation\HasManyThrough', $roles3);
        $this->assertCount(2, $roles3);
        $this->assertSame($rows, $roles3->toArray());

        $this->assertNotSame($roles, $roles2);
        $this->assertNotSame($roles, $roles3);
        $this->assertNotSame($roles2, $roles3);
    }

    public function testEagerLoadingAndLazyLoading()
    {
        $rows = [
            ['id' => 3, 'name' => 'foo', 'user_id' => 1],
            ['id' => 4, 'name' => 'bar', 'user_id' => 2],
            ['id' => 5, 'name' => 'baz', 'user_id' => 1],
            ['id' => 6, 'name' => 'qux', 'user_id' => 2],
        ];

        $stmt = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($rows);

        $stmt2 = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt2->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn([
            ['id' => 3, 'description' => 'description1'],
            ['id' => 5, 'description' => 'description2'],
        ]);

        $pdo = $this->mockPdo('query');
        $pdo->expects($this->at(0))->method('query')
            ->with("SELECT roles.id, roles.name, user_roles.user_id FROM roles INNER JOIN user_roles ON user_roles.role_id = roles.id WHERE (user_roles.user_id IN(1, 2, 3))")
            ->willReturn($stmt);

        $pdo->expects($this->at(1))->method('query')
            ->with("SELECT roles.id, roles.description FROM roles WHERE (roles.id IN(3, 5))")
            ->willReturn($stmt2);

        $connection = $this->createConnection($pdo);

        User::setConnection($connection);

        $users = User::all();
        $users[0] = $user1 = new User(['id' => 1], $users);
        $users[1] = $user2 = new User(['id' => 2], $users);
        $users[2] = $user3 = new User(['id' => 3], $users);

        $this->assertSame([['id' => 3, 'name' => 'foo'], ['id' => 5, 'name' => 'baz']], $user1->Roles->toArray());
        $this->assertSame([['id' => 4, 'name' => 'bar'], ['id' => 6, 'name' => 'qux']], $user2->Roles->toArray());
        $this->assertSame([], $user3->Roles->toArray());

        $this->assertSame('description1',$user1->Roles[0]->description);
        $this->assertSame('description2',$user1->Roles[1]->description);
    }

    public function testWithNoCacheShouldNotInvolveEagerLoading()
    {
        $rows1 = [
            ['id' => 3, 'name' => 'foo'],
            ['id' => 5, 'name' => 'baz'],
        ];

        $rows2 = [
            ['id' => 4, 'name' => 'bar'],
            ['id' => 6, 'name' => 'qux'],
        ];

        $stmt1 = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt1->expects($this->once())->method('fetchAll')->willReturn($rows1);

        $stmt2 = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt2->expects($this->once())->method('fetchAll')->willReturn($rows2);


        $pdo = $this->getMock('SlothsTest\Db\PDOMock', ['query']);

        $pdo->expects($this->at(0))->method('query')
            ->with("SELECT roles.id, roles.name FROM roles INNER JOIN user_roles ON user_roles.role_id = roles.id WHERE (user_roles.user_id = 1)")
            ->willReturn($stmt1);
        $pdo->expects($this->at(1))->method('query')
            ->with("SELECT roles.id, roles.name FROM roles INNER JOIN user_roles ON user_roles.role_id = roles.id WHERE (user_roles.user_id = 2)")
            ->willReturn($stmt2);

        $connection = $this->createConnection($pdo);

        User::setConnection($connection);

        $users = User::all();
        $users[0] = $user1 = new User(['id' => 1], $users);
        $users[1] = $user2 = new User(['id' => 2], $users);

        $this->assertSame($rows1, $user1->Roles()->toArray());
        $this->assertSame($rows2, $user2->Roles()->toArray());
    }
}