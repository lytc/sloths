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
        $storage->expects($this->once())->method('set')->with('foo', 'bar', 'expiration');
        $storage->expects($this->once())->method('remove')->with('foo');
        $storage->expects($this->once())->method('removeAll');
        $storage->expects($this->once())->method('replace')->with('foo', 'bar');

        $cacheManager = new CacheManager();
        $cacheManager->setStorage($storage);

        $this->assertTrue($cacheManager->has('foo'));
        $this->assertSame('bar', $cacheManager->get('foo'));
        $this->assertSame($cacheManager, $cacheManager->set('foo', 'bar', 'expiration'));
        $this->assertSame($cacheManager, $cacheManager->remove('foo'));
        $this->assertSame($cacheManager, $cacheManager->removeAll());
        $this->assertSame($cacheManager, $cacheManager->replace('foo', 'bar'));
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