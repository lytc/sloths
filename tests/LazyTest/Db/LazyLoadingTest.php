<?php

namespace LazyTest\Db;

use LazyTest\Db\Model\Post;

/**
 * @covers Lazy\Db\AbstractModel
 */
class LazyLoadingTest extends TestCase
{
    public function test()
    {
        $post = Post::first(1, 'id, name');
        $this->assertEquals(array('id' => 1, 'name' => 'name1'), $post->toArray());
        $this->assertSame('content1', $post->content);
    }
}