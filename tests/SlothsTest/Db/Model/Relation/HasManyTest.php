<?php

namespace SlothsTest\Db\Model\Relation;

use MockModel\User;
use Sloths\Db\ConnectionManager;
use Sloths\Db\Model\Collection;

class HasManyTest extends TestCase
{
    public function test()
    {
        $rows = [
            ['id' => 2, 'user_id' => 1],
            ['id' => 3, 'user_id' => 1],
        ];

        $stmt = $this->getMock('stmt', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($rows);

        $connection = $this->getMock('Sloths\Db\Connection', ['query'], ['dsn']);
        $connection->expects($this->once())->method('query')
            ->with("SELECT posts.id, posts.user_id, posts.title FROM posts WHERE (posts.user_id = 1)")->willReturn($stmt);

        $connectionManager = new ConnectionManager();
        $connectionManager->setConnection($connection);


        $user = new User(['id' => 1]);
        $user->setDefaultConnectionManager($connectionManager);

        $posts = $user->getHasMany('Posts');
        $this->assertSame($rows, $posts->toArray());

        $this->assertTrue($user->hasHasMany('Posts'));
    }

    public function testWithParentCollection()
    {
        $userRows = [
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
        ];

        $postRows = [
            ['id' => 1, 'user_id' => 1, 'title' => 'foo'],
            ['id' => 2, 'user_id' => 1, 'title' => 'bar'],
            ['id' => 3, 'user_id' => 2, 'title' => 'baz'],
        ];

        $users = new Collection($userRows, new User());
        $user1 = $users->getAt(0);
        $user2 = $users->getAt(1);
        $user3 = $users->getAt(2);

        $stmt = $this->getMock('stmt', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($postRows);

        $connection = $this->getMock('Sloths\Db\Connection', ['query'], ['dsn']);
        $connection->expects($this->once())->method('query')
            ->with("SELECT posts.id, posts.user_id, posts.title FROM posts WHERE (posts.user_id IN (1, 2, 3))")
            ->willReturn($stmt)
        ;

        $connectionManager = new ConnectionManager();
        $connectionManager->setConnection($connection);

        $user1->setDefaultConnectionManager($connectionManager);

        $posts = $user1->getHasMany('Posts');
        $this->assertSame([['id' => 1, 'user_id' => 1, 'title' => 'foo'], ['id' => 2, 'user_id' => 1, 'title' => 'bar']], $posts->toArray());
        $this->assertSame([['id' => 3, 'user_id' => 2, 'title' => 'baz']], $user2->getRelation('Posts', true)->toArray());
        $this->assertSame([], $user3->getRelation('Posts', true)->toArray());
    }
}