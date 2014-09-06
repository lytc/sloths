<?php

namespace SlothsTest\Db\Model;

use Sloths\Db\Database;
use Sloths\Db\Model\Collection;
use SlothsTest\Db\Model\Stub\Post;
use SlothsTest\TestCase;

class LazyLoadingTest extends TestCase
{
    public function test()
    {
        $stmt = $this->getMock('stmt', ['fetchColumn']);
        $stmt->expects($this->once())->method('fetchColumn')->willReturn('foo');

        $pdo = $this->getMock('mockpdo', ['query']);
        $pdo->expects($this->once())->method('query')
            ->with("SELECT posts.content FROM posts WHERE (posts.id = 1)")
            ->willReturn($stmt)
        ;

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->once())->method('getPdo')->willReturn($pdo);

        $database = new Database();
        $database->setConnection($connection);

        Post::setDatabase($database);

        $post = new Post(['id' => 1]);

        $this->assertSame('foo', $post->content);
    }

    public function testWithParentCollection()
    {
        $stmt = $this->getMock('stmt', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')
            ->with(\PDO::FETCH_GROUP|\PDO::FETCH_COLUMN)
            ->willReturn([1 => 'foo', 2 => 'bar']);

        $pdo = $this->getMock('mockpdo', ['query']);
        $pdo->expects($this->once())->method('query')
            ->with("SELECT posts.id, posts.content FROM posts WHERE (posts.id IN (1, 2))")
            ->willReturn($stmt)
        ;

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->once())->method('getPdo')->willReturn($pdo);

        $database = new Database();
        $database->setConnection($connection);

        Post::setDatabase($database);

        $posts = new Collection([['id' => 1], ['id' => 2]], __NAMESPACE__ . '\Stub\Post');
        $post1 = $posts->getAt(0);
        $post2 = $posts->getAt(1);

        $this->assertSame('foo', $post1->content);
        $this->assertSame('bar', $post2->content);
    }
}