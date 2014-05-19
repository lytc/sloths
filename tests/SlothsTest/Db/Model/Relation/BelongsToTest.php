<?php

namespace SlothsTest\Db\Model\Relation;

use SlothsTest\Db\Model\Stub\Post;
use SlothsTest\Db\Model\TestCase;

class BelongsToTest extends TestCase
{
    public function testInstance()
    {
        $stmt = $this->mock('PDOStatement');
        $stmt->shouldReceive('fetch')->once()->with(\PDO::FETCH_ASSOC)->andReturn(['id' => 1]);
        $pdo = $this->mockPdo();
        $pdo->shouldReceive('query')->once()->andReturn($stmt);

        Post::setConnection($this->createConnection($pdo));

        $post = new Post(['id' => 1, 'created_user_id' => 1]);
        $user = $post->User;
        $this->assertInstanceOf('SlothsTest\Db\Model\Stub\User', $user);
        $this->assertSame(1, $user->id());
    }

    public function testEagerLoadingAndLazyLoading()
    {
        $posts = Post::all();
        $posts[0] = $post1 = new Post(['id' => 1, 'created_user_id' => 1], $posts);
        $posts[1] = $post2 = new Post(['id' => 2, 'created_user_id' => 2], $posts);
        $posts[2] = $post3 = new Post(['id' => 3, 'created_user_id' => 1], $posts);

        $stmt = $this->mock('PDOStatement');
        $stmt->shouldReceive('fetchAll')->once()->with(\PDO::FETCH_ASSOC)->andReturn([
            ['id' => 1],
            ['id' => 2],
        ]);

        $stmt2 = $this->mock('PDOStatement');
        $stmt2->shouldReceive('fetchAll')->once()->with(\PDO::FETCH_ASSOC)->andReturn([
            ['id' => 1, 'profile' => 'foo'],
            ['id' => 2, 'profile' => 'bar'],
        ]);

        $pdo = $this->mockPdo();
        $pdo->shouldReceive('query')->once()
            ->with("SELECT users.id, users.name, users.password, users.created_time FROM users WHERE (users.id IN(1, 2))")
            ->andReturn($stmt);

        $pdo->shouldReceive('query')->once()
            ->with("SELECT users.id, users.profile FROM users WHERE (users.id IN(1, 2))")
            ->andReturn($stmt2);

        Post::setConnection($this->createConnection($pdo));

        $this->assertSame($post1->CreatedUser, $post3->CreatedUser);
        $this->assertSame('foo', $post1->CreatedUser->profile);
        $this->assertSame('bar', $post2->CreatedUser->profile);
    }
}