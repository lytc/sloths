<?php

namespace SlothsTest\Db\Model;

use Sloths\Db\Database;
use SlothsTest\Db\Model\Stub\User;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Db\Model\AbstractModel
 */
class ModelRelationTest extends TestCase
{
    public function testHasOneRelation()
    {
        $row = [
            'id' => 1,
            'user_id' => 1,
            'resume' => 'resume'
        ];

        $stmt = $this->getMock('stmt', ['fetch']);
        $stmt->expects($this->atLeast(1))->method('fetch')->with(\PDO::FETCH_ASSOC)->willReturn($row);

        $pdo = $this->getMock('mockpdo', ['query']);
        $pdo->expects($this->atLeast(1))->method('query')->with("SELECT profiles.* FROM profiles WHERE (profiles.user_id = 1) LIMIT 1")->willReturn($stmt);

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->atLeast(1))->method('getPdo')->willReturn($pdo);

        $database = new Database();
        $database->setConnection($connection);

        User::setDatabase($database);

        $user = new User(['id' => 1]);
        $profile = $user->Profile;

        $this->assertInstanceOf('SlothsTest\Db\Model\Stub\Profile', $profile);
        $this->assertSame($row, $profile->toArray());

        $this->assertSame($profile, $user->Profile);

        $profileNoCache = $user->Profile();
        $this->assertSame($row, $profileNoCache->toArray());

        $this->assertNotSame($profile, $profileNoCache);
    }

    public function testBelongsToRelation()
    {
        $row = [
            'id' => 1,
            'name' => 'Admin'
        ];

        $stmt = $this->getMock('stmt', ['fetch']);
        $stmt->expects($this->atLeast(1))->method('fetch')->with(\PDO::FETCH_ASSOC)->willReturn($row);

        $pdo = $this->getMock('mockpdo', ['query']);
        $pdo->expects($this->atLeast(1))->method('query')
            ->with("SELECT groups.* FROM groups WHERE (groups.id = 1) LIMIT 1")
            ->willReturn($stmt);

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->atLeast(1))->method('getPdo')->willReturn($pdo);

        $database = new Database();
        $database->setConnection($connection);

        User::setDatabase($database);

        $user = new User(['id' => 1, 'group_id' => 1]);
        $group = $user->Group;

        $this->assertInstanceOf('SlothsTest\Db\Model\Stub\Group', $group);
        $this->assertSame($row, $group->toArray());

        $this->assertSame($group, $user->Group);

        $groupNoCache = $user->Group();
        $this->assertSame($row, $groupNoCache->toArray());

        $this->assertNotSame($group, $groupNoCache);
    }

    public function testHasManyRelation()
    {
        $rows = [
            ['id' => 1, 'user_id' => 1, 'title' => 'Post 1'],
            ['id' => 2,'user_id' => 1, 'title' => 'Post 2'],
        ];

        $stmt = $this->getMock('stmt', ['fetchAll']);
        $stmt->expects($this->atLeast(1))->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($rows);

        $pdo = $this->getMock('mockpdo', ['query']);
        $pdo->expects($this->atLeast(1))->method('query')
            ->with("SELECT posts.id, posts.user_id, posts.title FROM posts WHERE (posts.user_id = 1)")->willReturn($stmt);

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->atLeast(1))->method('getPdo')->willReturn($pdo);

        $database = new Database();
        $database->setConnection($connection);

        User::setDatabase($database);

        $user = new User(['id' => 1]);
        $posts = $user->Posts;

        $this->assertInstanceOf('Sloths\Db\Model\Collection', $posts);
        $this->assertSame($rows, $posts->toArray());

        $this->assertSame($posts, $user->Posts);

        $postsNoCache = $user->Posts();
        $this->assertSame($rows, $postsNoCache->toArray());

        $this->assertNotSame($posts, $postsNoCache);
    }

    public function testHasManyThroughRelation()
    {
        $rows = [
            ['id' => 1, 'name' => 'role 1'],
            ['id' => 2, 'name' => 'role 2'],
        ];

        $stmt = $this->getMock('stmt', ['fetchAll']);
        $stmt->expects($this->atLeast(1))->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($rows);

        $pdo = $this->getMock('mockpdo', ['query']);
        $pdo->expects($this->atLeast(1))->method('query')
            ->with("SELECT roles.id, roles.name, user_roles.user_id FROM roles INNER JOIN user_roles ON ((user_roles.role_id = roles.id) AND (user_roles.user_id = 1))")
            ->willReturn($stmt);

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->atLeast(1))->method('getPdo')->willReturn($pdo);

        $database = new Database();
        $database->setConnection($connection);

        User::setDatabase($database);

        $user = new User(['id' => 1]);
        $roles = $user->Roles;

        $this->assertInstanceOf('Sloths\Db\Model\Collection', $roles);
        $this->assertSame($rows, $roles->toArray());

        $this->assertSame($roles, $user->Roles);

        $rolesNoCache = $user->Roles();
        $this->assertSame($rows, $rolesNoCache->toArray());

        $this->assertNotSame($roles, $rolesNoCache);
    }
}