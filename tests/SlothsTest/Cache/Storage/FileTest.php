<?php

namespace SlothsTest\Cache\Storage;

use Sloths\Cache\Storage\File;
use SlothsTest\TestCase;


/**
 * @covers Sloths\Cache\Storage\File
 */
class FileTest extends TestCase
{
    public function test()
    {
        $directory = sys_get_temp_dir() . '/' . uniqid();
        mkdir($directory);

        $storage = new File();
        $storage->setDirectory($directory);

        $this->assertFalse($storage->has('foo'));

        $storage->set('foo', 'bar', strtotime('+1 day'));
        $this->assertTrue($storage->has('foo'));
        $this->assertSame('bar', $storage->get('foo'));

        $storage->remove('foo');
        $this->assertFalse($storage->has('foo'));
    }

    public function testHasWillReturnsFalseIfExpired()
    {
        $directory = sys_get_temp_dir() . '/' . uniqid();
        mkdir($directory);

        $storage = new File();
        $storage->setDirectory($directory);
        $storage->set('foo', 'bar', strtotime('-1 day'));

        $this->assertFalse($storage->has('foo'));
    }

    public function testGetWithExpired()
    {
        $directory = sys_get_temp_dir() . '/' . uniqid();
        mkdir($directory);

        $storage = new File();
        $storage->setDirectory($directory);
        $storage->set('foo', 'bar', strtotime('-1 day'));

        $this->assertNull($storage->get('foo', $success));
        $this->assertFalse($success);
    }

    public function testRemoveAll()
    {
        $directory = sys_get_temp_dir() . '/' . uniqid();
        mkdir($directory);

        $storage = new File();
        $storage->setDirectory($directory);

        $this->assertFalse($storage->has('foo'));

        $storage->set('foo', 'bar', strtotime('+1 day'));
        $this->assertTrue($storage->has('foo'));

        $storage->removeAll();
        $this->assertFalse($storage->has('foo'));
    }

    public function testReplace()
    {
        $directory = sys_get_temp_dir() . '/' . uniqid();
        mkdir($directory);

        $storage = new File();
        $storage->setDirectory($directory);

        $this->assertFalse($storage->replace('foo', 'bar'));

        $storage->set('foo', 'bar', strtotime('+1 day'));
        $storage->replace('foo', 'baz', strtotime('+2 day'));

        $this->assertSame('baz', $storage->get('foo'));
    }
}