<?php

namespace LazyTest\Db;

use LazyTest\Db\Model\Post;

/**
 * @covers Lazy\Db\AbstractModel
 */
class ManyToOneTest extends TestCase
{
    public function test()
    {
        $post = Post::first();
        $user = $post->User;
        $this->assertInstanceOf('LazyTest\Db\Model\User', $user);
    }
}