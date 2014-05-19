<?php

namespace SlothsTest\Db\Model\Relation;

use SlothsTest\Db\Model\Stub\User;
use SlothsTest\Db\Model\TestCase;

class HasManyTest extends TestCase
{
    public function testInstance()
    {
        $rows = [
            ['id' => 1, 'created_user_id' => 1, 'modified_user_id' => null, 'name' => 'foo'],
            ['id' => 2, 'created_user_id' => 1, 'modified_user_id' => 1, 'name' => 'bar'],
        ];

        $stmt = $this->mock('PDOStatement');
        $stmt->shouldReceive('fetchAll')->once()->with(\PDO::FETCH_ASSOC)->andReturn($rows);

        $pdo = $this->mockPdo();
        $pdo->shouldReceive('query')
            ->once()
            ->with("SELECT posts.id, posts.created_user_id, posts.modified_user_id, posts.name FROM posts WHERE (posts.created_user_id = 1)")
            ->andReturn($stmt);

        $connection = $this->createConnection($pdo);
        User::setConnection($connection);

        $user = new User(['id' => 1]);
        $posts = $user->Posts;
        $this->assertInstanceOf('Sloths\Db\Model\Relation\HasMany', $posts);

        $this->assertSame($posts, $user->Posts);
        $this->assertCount(2, $user->Posts);
    }

    public function testEagerLoadAndLazyLoad()
    {
        $postRows = [
            ['id' => 3, 'created_user_id' => 1, 'modified_user_id' => 1, 'name' => 'foo'],
            ['id' => 4, 'created_user_id' => 2, 'modified_user_id' => 1, 'name' => 'bar'],
            ['id' => 5, 'created_user_id' => 1, 'modified_user_id' => 1, 'name' => 'baz'],
            ['id' => 6, 'created_user_id' => 2, 'modified_user_id' => 1, 'name' => 'qux'],
        ];

        $stmt = $this->mock('PDOStatement');
        $stmt->shouldReceive('fetchAll')->once()->with(\PDO::FETCH_ASSOC)->andReturn($postRows);

        $pdo = $this->mockPdo();
        $pdo->shouldReceive('query')
            ->once()
            ->with("SELECT posts.id, posts.created_user_id, posts.modified_user_id, posts.name FROM posts WHERE (posts.created_user_id IN(1, 2, 3))")
            ->andReturn($stmt);

        $stmt2 = $this->mock('PDOStatement');
        $stmt2->shouldReceive('fetchAll')->once()->with(\PDO::FETCH_ASSOC)->andReturn([
            ['id' => 3, 'content' => 'content1'],
            ['id' => 5, 'content' => 'content2'],
        ]);

        $pdo->shouldReceive('query')->once()->with("SELECT posts.id, posts.content FROM posts WHERE (posts.id IN(3, 5))")
            ->andReturn($stmt2);

        $connection = $this->createConnection($pdo);

        User::setConnection($connection);

        $users = User::all();
        $users[0] = $user1 = new User(['id' => 1], $users);
        $users[1] = $user2 = new User(['id' => 2], $users);
        $users[1] = $user3 = new User(['id' => 3], $users);

        $this->assertSame([
            ['id' => 3, 'created_user_id' => 1, 'modified_user_id' => 1, 'name' => 'foo'],
            ['id' => 5, 'created_user_id' => 1, 'modified_user_id' => 1, 'name' => 'baz']
        ], $user1->Posts->toArray());

        $this->assertSame([
            ['id' => 4, 'created_user_id' => 2, 'modified_user_id' => 1, 'name' => 'bar'],
            ['id' => 6, 'created_user_id' => 2, 'modified_user_id' => 1, 'name' => 'qux']
        ], $user2->Posts->toArray());

        $this->assertSame([], $user3->Posts->toArray());

        $this->assertSame('content1', $user1->Posts[0]->content);
        $this->assertSame('content2', $user1->Posts[1]->content);
    }
}