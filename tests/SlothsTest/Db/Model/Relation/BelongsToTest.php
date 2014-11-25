<?php

namespace SlothsTest\Db\Model\Relation;

use MockModel\Post;
use Sloths\Db\ConnectionManager;
use Sloths\Db\Model\Collection;

class BelongsToTest extends TestCase
{
    public function test()
    {
        $rows = [
            ['id' => 2, 'name' => 'foo']
        ];

        $stmt = $this->getMock('stmt', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($rows);

        $connection = $this->getMock('Sloths\Db\Connection', ['query'], ['dsn']);
        $connection->expects($this->once())
            ->method('query')->with("SELECT users.* FROM users WHERE (users.id = 2) LIMIT 1")->willReturn($stmt);

        $connectionManager = new ConnectionManager();
        $connectionManager->setConnection($connection);

        $post = new Post(['id' => 1, 'user_id' => 2]);
        Post::setDefaultConnectionManager($connectionManager);

        $user = $post->getBelongsTo('User');
        $this->assertSame($rows[0], $user->toArray());

        $this->assertFalse($post->getBelongsToSchema('User')->touchOnSave());
        $this->assertTrue($post->hasBelongsTo('User'));
    }

    public function testWithParentCollection()
    {
        $postRows = [
            ['id' => 1, 'user_id' => 2],
            ['id' => 2, 'user_id' => 2],
            ['id' => 3, 'user_id' => 3],
            ['id' => 4, 'user_id' => null],
        ];

        $posts = new Collection($postRows, new Post());
        $post1 = $posts->getAt(0);
        $post2 = $posts->getAt(1);
        $post3 = $posts->getAt(2);
        $post4 = $posts->getAt(3);

        $userRows = [
            ['id' => 2, 'name' => 'foo'],
            ['id' => 3, 'name' => 'bar'],
        ];

        $stmt = $this->getMock('stmt', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($userRows);

        $connection = $this->getMock('Sloths\Db\Connection', ['query'], ['dsn']);
        $connection->expects($this->once())
            ->method('query')->with("SELECT users.* FROM users WHERE (users.id IN (2, 3))")->willReturn($stmt);

        $connectionManager = new ConnectionManager();
        $connectionManager->setConnection($connection);
        $post1->setDefaultConnectionManager($connectionManager);

        $user = $post1->getBelongsTo('User');
        $this->assertSame($userRows[0], $user->toArray());
        $this->assertSame($user, $post2->getRelation('User', true));
        $this->assertSame($userRows[1], $post3->getRelation('User', true)->toArray());
        $this->assertNull($post4->getRelation('User', true));
    }
}