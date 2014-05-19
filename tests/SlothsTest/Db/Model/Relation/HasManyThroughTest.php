<?php

namespace SlothsTest\Db\Model\Relation;

use SlothsTest\Db\Model\Stub\User;
use SlothsTest\Db\Model\TestCase;

class HasManyThroughTest extends TestCase
{
    public function testInstance()
    {
        $rows = [
            ['id' => 1, 'name' => 'foo'],
            ['id' => 2, 'name' => 'bar'],
        ];

        $stmt = $this->mock('PDOStatement');
        $stmt->shouldReceive('fetchAll')->once()
            ->with(\PDO::FETCH_ASSOC)
            ->andReturn($rows);

        $pdo = $this->mockPdo();
        $pdo->shouldReceive('query')->once()
            ->with("SELECT roles.id, roles.name FROM roles INNER JOIN user_roles ON user_roles.role_id = roles.id WHERE (user_roles.user_id = 1)")
            ->andReturn($stmt);

        $connection = $this->createConnection($pdo);

        User::setConnection($connection);

        $user = new User(['id' => 1]);
        $roles = $user->Roles;
        $this->assertInstanceOf('Sloths\Db\Model\Relation\HasManyThrough', $roles);
        $this->assertCount(2, $roles);
        $this->assertSame($rows, $roles->toArray());
    }

    public function testEagerLoadingAndLazyLoading()
    {
        $rows = [
            ['id' => 3, 'name' => 'foo', 'user_id' => 1],
            ['id' => 4, 'name' => 'bar', 'user_id' => 2],
            ['id' => 5, 'name' => 'baz', 'user_id' => 1],
            ['id' => 6, 'name' => 'qux', 'user_id' => 2],
        ];

        $stmt = $this->mock('PDOStatement');
        $stmt->shouldReceive('fetchAll')->once()
            ->with(\PDO::FETCH_ASSOC)
            ->andReturn($rows);

        $stmt2 = $this->mock('PDOStatement');
        $stmt2->shouldReceive('fetchAll')->once()->with(\PDO::FETCH_ASSOC)->andReturn([
            ['id' => 3, 'description' => 'description1'],
            ['id' => 5, 'description' => 'description2'],
        ]);

        $pdo = $this->mockPdo();
        $pdo->shouldReceive('query')->once()
            ->with("SELECT roles.id, roles.name, user_roles.user_id FROM roles INNER JOIN user_roles ON user_roles.role_id = roles.id WHERE (user_roles.user_id IN(1, 2, 3))")
            ->andReturn($stmt);

        $pdo->shouldReceive('query')->once()
            ->with("SELECT roles.id, roles.description FROM roles WHERE (roles.id IN(3, 5))")
            ->andReturn($stmt2);

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
}