<?php

namespace LazyTest\Db;

use LazyTest\Db\Model\User;

/**
 * @covers Lazy\Db\AbstractModel
 */
class OneToManyTest extends TestCase
{
    public function test()
    {
        $user = User::first(1);
        $posts = $user->Posts;
        $this->assertInstanceOf('Lazy\Db\Collection', $posts);
        $this->assertSame($posts, $user->Posts);

        $expectedSql = "SELECT posts.id, posts.user_id, posts.name FROM posts WHERE (user_id = '1')";
        $this->assertSame($expectedSql, $posts->getSqlSelect()->toString());
    }
}