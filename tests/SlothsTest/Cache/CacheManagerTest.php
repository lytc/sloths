<?php

namespace SlothsTest\Cache;

use SlothsTest\TestCase;
use Sloths\Cache\CacheManager;

/**
 * @covers Sloths\Cache\CacheManager
 */
class CacheManagerTest extends TestCase
{
    public function test()
    {
        $storage = $this->getMock('Sloths\Cache\Storage\StorageInterface');
        $storage->expects($this->once())->method('has')->with('foo')->willReturn(true);
        $storage->expects($this->once())->method('get')->with('foo')->willReturn('bar');
        $storage->expects($this->once())->method('set')->with('foo', 'bar', 10);
        $storage->expects($this->once())->method('remove')->with('foo');
        $storage->expects($this->once())->method('removeAll');
        $storage->expects($this->once())->method('replace')->with('foo', 'bar');

        $cacheManager = new CacheManager();
        $cacheManager->setStorage($storage);

        $this->assertTrue($cacheManager->has('foo'));
        $this->assertSame('bar', $cacheManager->get('foo'));
        $this->assertSame($cacheManager, $cacheManager->set('foo', 'bar', 10));
        $this->assertSame($cacheManager, $cacheManager->remove('foo'));
        $this->assertSame($cacheManager, $cacheManager->removeAll());
        $this->assertSame($cacheManager, $cacheManager->replace('foo', 'bar'));
    }

    public function testSetAndReplaceWithExpirationString()
    {
        $storage = $this->getMock('Sloths\Cache\Storage\StorageInterface');
        $storage->expects($this->once())->method('set')->with('foo', 'bar', strtotime('+10 minutes'));
        $storage->expects($this->once())->method('replace')->with('foo', 'bar', strtotime('+20 minutes'));

        $cacheManager = new CacheManager();
        $cacheManager->setStorage($storage);

        $cacheManager->set('foo', 'bar', '+10 minutes');
        $cacheManager->replace('foo', 'bar', '+20 minutes');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetStorageShouldThrowExceptionIfHasNoStorageWithStrictMode()
    {
        $cacheManager = new CacheManager();
        $cacheManager->getStorage();
    }
}