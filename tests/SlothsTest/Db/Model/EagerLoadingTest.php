<?php

namespace SlothsTest\Db\Model;

use Sloths\Application\Service\Database;
use SlothsTest\TestCase;
use SlothsTest\Db\Model\Stub\User;

/**
 * @covers Sloths\Db\Model\AbstractModel
 */
class EagerLoadingTest extends TestCase
{
    public function testWithHasOne()
    {
        $userRows = [
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
        ];

        $profileRows = [
            ['id' => 1, 'user_id' => 1, 'title' => 'title 1'],
            ['id' => 2, 'user_id' => 2, 'title' => 'title 2'],
        ];

        $stmt1 = $this->getMock('stmt', ['fetchAll']);
        $stmt1->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($userRows);

        $stmt2 = $this->getMock('stmt', ['fetchAll']);
        $stmt2->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($profileRows);

        $pdo = $this->getMock('mockpdo', ['query']);
        $pdo->expects($this->at(0))->method('query')
            ->with("SELECT users.id, users.username, users.password, users.created_time, users.modified_time FROM users")
            ->willReturn($stmt1)
        ;

        $pdo->expects($this->at(1))->method('query')
            ->with("SELECT profiles.id, profiles.user_id, profiles.title FROM profiles WHERE (profiles.user_id IN (1, 2, 3))")
            ->willReturn($stmt2)
        ;

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->atLeast(1))->method('getPdo')->willReturn($pdo);

        $database = new Database();
        $database->setConnection($connection);

        User::setDatabase($database);

        $users = User::all();
        $user1 = $users->getAt(0);
        $user2 = $users->getAt(1);
        $user3 = $users->getAt(2);

        $this->assertSame('title 1', $user1->Profile->title);
        $this->assertSame('title 2', $user2->Profile->title);
        $this->assertNull($user3->Profile);
    }

    public function testWithBelongsTo()
    {
        $userRows = [
            ['id' => 1, 'group_id' => 1],
            ['id' => 2, 'group_id' => 2],
            ['id' => 3, 'group_id' => 2],
        ];

        $profileRows = [
            ['id' => 1, 'name' => 'group 1'],
            ['id' => 2, 'name' => 'group 2'],
        ];

        $stmt1 = $this->getMock('stmt', ['fetchAll']);
        $stmt1->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($userRows);

        $stmt2 = $this->getMock('stmt', ['fetchAll']);
        $stmt2->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($profileRows);

        $pdo = $this->getMock('mockpdo', ['query']);
        $pdo->expects($this->at(0))->method('query')
            ->with("SELECT users.id, users.username, users.password, users.created_time, users.modified_time FROM users")
            ->willReturn($stmt1)
        ;

        $pdo->expects($this->at(1))->method('query')
            ->with("SELECT groups.id, groups.name FROM groups WHERE (groups.id IN (1, 2))")
            ->willReturn($stmt2)
        ;

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->atLeast(1))->method('getPdo')->willReturn($pdo);

        $database = new Database();
        $database->setConnection($connection);

        User::setDatabase($database);

        $users = User::all();
        $user1 = $users->getAt(0);
        $user2 = $users->getAt(1);
        $user3 = $users->getAt(1);

        $this->assertSame('group 1', $user1->Group->name);
        $this->assertSame('group 2', $user2->Group->name);
        $this->assertSame($user2->Group, $user3->Group);
    }

    public function testWithHasMany()
    {
        $userRows = [
            ['id' => 1],
            ['id' => 2],
        ];

        $postRows = [
            ['id' => 1, 'user_id' => 1],
            ['id' => 2, 'user_id' => 2],
            ['id' => 3, 'user_id' => 1],
        ];

        $postRows2 = [
            ['id' => 2, 'user_id' => 2],
        ];

        $stmt1 = $this->getMock('stmt', ['fetchAll']);
        $stmt1->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($userRows);

        $stmt2 = $this->getMock('stmt', ['fetchAll']);
        $stmt2->expects($this->at(0))->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($postRows);

        $stmt3 = $this->getMock('stmt', ['fetchAll']);
        $stmt3->expects($this->at(0))->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($postRows2);

        $pdo = $this->getMock('mockpdo', ['query']);
        $pdo->expects($this->at(0))->method('query')
            ->with("SELECT users.id, users.username, users.password, users.created_time, users.modified_time FROM users")
            ->willReturn($stmt1)
        ;

        $pdo->expects($this->at(1))->method('query')
            ->with("SELECT posts.id, posts.user_id, posts.title FROM posts WHERE (posts.user_id IN(1, 2))")
            ->willReturn($stmt2)
        ;

        $pdo->expects($this->at(2))->method('query')
            ->with("SELECT posts.id, posts.user_id, posts.title FROM posts WHERE (posts.user_id = 2)")
            ->willReturn($stmt3)
        ;

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->atLeast(1))->method('getPdo')->willReturn($pdo);

        $database = new Database();
        $database->setConnection($connection);

        User::setDatabase($database);

        $users = User::all();

        $user1 = $users->first();
        $user2 = $users->getAt(1);

        $this->assertSame([['id' => 1, 'user_id' => 1], ['id' => 3, 'user_id' => 1]], $user1->Posts->toArray());
        $this->assertSame([['id' => 2, 'user_id' => 2]], $user2->Posts->toArray());

        $this->assertSame($postRows2, $user2->Posts()->toArray());
    }

    public function testWithHasManyThrough()
    {
        $userRows = [
            ['id' => 1],
            ['id' => 2],
        ];

        $roleRows = [
            ['id' => 1, 'name' => 'role 1', 'user_id' => 1],
            ['id' => 2, 'name' => 'role 2', 'user_id' => 2],
            ['id' => 3, 'name' => 'role 3', 'user_id' => 1],
        ];

        $stmt1 = $this->getMock('stmt', ['fetchAll']);
        $stmt1->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($userRows);

        $stmt2 = $this->getMock('stmt', ['fetchAll']);
        $stmt2->expects($this->at(0))->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($roleRows);

        $pdo = $this->getMock('mockpdo', ['query']);
        $pdo->expects($this->at(0))->method('query')
            ->with("SELECT users.id, users.username, users.password, users.created_time, users.modified_time FROM users")
            ->willReturn($stmt1)
        ;

        $pdo->expects($this->at(1))->method('query')
            ->with("SELECT roles.id, roles.name, user_roles.user_id FROM roles INNER JOIN user_roles ON ((user_roles.role_id = roles.id) AND (user_roles.user_id IN(1, 2)))")
            ->willReturn($stmt2)
        ;

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->atLeast(1))->method('getPdo')->willReturn($pdo);

        $database = new Database();
        $database->setConnection($connection);

        User::setDatabase($database);

        $users = User::all();

        $user1 = $users->first();
        $user2 = $users->getAt(1);

        $this->assertSame([['id' => 1, 'name' => 'role 1', 'user_id' => 1], ['id' => 3, 'name' => 'role 3', 'user_id' => 1]], $user1->Roles->toArray());
        $this->assertSame([['id' => 2, 'name' => 'role 2', 'user_id' => 2]], $user2->Roles->toArray());
    }
}