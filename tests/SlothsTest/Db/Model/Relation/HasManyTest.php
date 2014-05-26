<?php

namespace SlothsTest\Db\Model\Relation;

use SlothsTest\Db\Model\Stub\User;
use SlothsTest\Db\Model\TestCase;

/**
 * @covers \Sloths\Db\Model\Model
 * @covers \Sloths\Db\Model\Relation\HasMany
 * @covers \Sloths\Db\Model\ArrayCollection
 */
class HasManyTest extends TestCase
{
    public function testWithCache()
    {
        $rows = [
            ['id' => 1, 'created_user_id' => 1, 'modified_user_id' => null, 'name' => 'foo'],
            ['id' => 2, 'created_user_id' => 1, 'modified_user_id' => 1, 'name' => 'bar'],
        ];

        $stmt = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($rows);

        $pdo = $this->mockPdo('query');
        $pdo->expects($this->once())->method('query')
            ->with("SELECT posts.id, posts.created_user_id, posts.modified_user_id, posts.name FROM posts WHERE (posts.created_user_id = 1)")
            ->willReturn($stmt);

        $connection = $this->createConnection($pdo);
        User::setConnection($connection);

        $user = new User(['id' => 1]);
        $posts = $user->Posts;
        $this->assertInstanceOf('Sloths\Db\Model\Relation\HasMany', $posts);

        $this->assertSame($posts, $user->Posts);
        $this->assertCount(2, $user->Posts);
    }

    public function testNoCache()
    {
        $rows = [
            ['id' => 1, 'created_user_id' => 1, 'modified_user_id' => null, 'name' => 'foo'],
            ['id' => 2, 'created_user_id' => 1, 'modified_user_id' => 1, 'name' => 'bar'],
        ];

        $stmt = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt->expects($this->exactly(3))->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($rows);

        $pdo = $this->mockPdo('query');
        $pdo->expects($this->exactly(3))->method('query')
            ->with("SELECT posts.id, posts.created_user_id, posts.modified_user_id, posts.name FROM posts WHERE (posts.created_user_id = 1)")
            ->willReturn($stmt);

        $connection = $this->createConnection($pdo);
        User::setConnection($connection);

        $user = new User(['id' => 1]);
        $posts = $user->Posts;
        $this->assertInstanceOf('Sloths\Db\Model\Relation\HasMany', $posts);
        $this->assertCount(2, $user->Posts);
        $this->assertSame($rows, $posts->toArray());


        $posts2 = $user->getRelation('Posts');
        $this->assertInstanceOf('Sloths\Db\Model\Relation\HasMany', $posts2);
        $this->assertCount(2, $posts2);
        $this->assertSame($rows, $posts2->toArray());

        $posts3 = $user->Posts();
        $this->assertInstanceOf('Sloths\Db\Model\Relation\HasMany', $posts3);
        $this->assertCount(2, $posts3);
        $this->assertSame($rows, $posts3->toArray());

        $this->assertNotSame($posts, $posts2);
        $this->assertNotSame($posts, $posts3);
        $this->assertNotSame($posts2, $posts3);
    }

    public function testEagerLoadAndLazyLoad()
    {
        $postRows = [
            ['id' => 3, 'created_user_id' => 1, 'modified_user_id' => 1, 'name' => 'foo'],
            ['id' => 4, 'created_user_id' => 2, 'modified_user_id' => 1, 'name' => 'bar'],
            ['id' => 5, 'created_user_id' => 1, 'modified_user_id' => 1, 'name' => 'baz'],
            ['id' => 6, 'created_user_id' => 2, 'modified_user_id' => 1, 'name' => 'qux'],
        ];

        $stmt = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($postRows);

        $pdo = $this->mockPdo('query');
        $pdo->expects($this->at(0))->method('query')
            ->with("SELECT posts.id, posts.created_user_id, posts.modified_user_id, posts.name FROM posts WHERE (posts.created_user_id IN(1, 2, 3))")
            ->willReturn($stmt);

        $stmt2 = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt2->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn([
            ['id' => 3, 'content' => 'content1'],
            ['id' => 5, 'content' => 'content2'],
        ]);

        $pdo->expects($this->at(1))->method('query')->with("SELECT posts.id, posts.content FROM posts WHERE (posts.id IN(3, 5))")
            ->willReturn($stmt2);

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

    public function testNoCacheShouldNotInvolveEagerLoad()
    {
        $postRows1 = [
            ['id' => 3, 'created_user_id' => 1, 'modified_user_id' => 1, 'name' => 'foo'],
            ['id' => 5, 'created_user_id' => 1, 'modified_user_id' => 1, 'name' => 'baz'],
        ];

        $postRows2 = [
            ['id' => 4, 'created_user_id' => 2, 'modified_user_id' => 1, 'name' => 'bar'],
            ['id' => 6, 'created_user_id' => 2, 'modified_user_id' => 1, 'name' => 'qux'],
        ];

        $stmt1 = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt1->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($postRows1);

        $stmt2 = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt2->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($postRows2);

        $pdo = $this->mockPdo('query');
        $pdo->expects($this->at(0))->method('query')
            ->with("SELECT posts.id, posts.created_user_id, posts.modified_user_id, posts.name FROM posts WHERE (posts.created_user_id = 1)")
            ->willReturn($stmt1);

        $pdo->expects($this->at(1))->method('query')
            ->with("SELECT posts.id, posts.created_user_id, posts.modified_user_id, posts.name FROM posts WHERE (posts.created_user_id = 2)")
            ->willReturn($stmt2);

        $connection = $this->createConnection($pdo);

        User::setConnection($connection);

        $users = User::all();
        $users[0] = $user1 = new User(['id' => 1], $users);
        $users[1] = $user2 = new User(['id' => 2], $users);

        $this->assertSame($postRows1, $user1->Posts()->toArray());
        $this->assertSame($postRows2, $user2->Posts()->toArray());
    }
}