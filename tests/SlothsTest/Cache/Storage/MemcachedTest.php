<?php

namespace SlothsTest\Cache\Storage;

use SlothsTest\TestCase;

/**
 * @covers Sloths\Cache\Storage\Memcached
 */
class MemcachedTest extends TestCase
{
    public function testGet()
    {
        $memcachedResource = $this->getMock('memcachedResource', ['get', 'getResultCode']);
        $memcachedResource->expects($this->once())->method('get')->with('foo')->willReturn('bar');
        $memcachedResource->expects($this->once())->method('getResultCode')->with()->willReturn(0);

        $memcached = $this->getMock('Sloths\Cache\Storage\Memcached', ['getMemcachedResource']);
        $memcached->expects($this->once())->method('getMemcachedResource')->willReturn($memcachedResource);

        /* @var $memcached  \Sloths\Cache\Storage\Memcached */
        $this->assertSame('bar', $memcached->get('foo', $success));
        $this->assertTrue($success);
    }

    public function testHas()
    {
        $memcachedResource = $this->getMock('memcachedResource', ['get', 'getResultCode']);
        $memcachedResource->expects($this->once())->method('get')->with('foo')->willReturn('bar');
        $memcachedResource->expects($this->once())->method('getResultCode')->with()->willReturn(0);

        $memcached = $this->getMock('Sloths\Cache\Storage\Memcached', ['getMemcachedResource']);
        $memcached->expects($this->once())->method('getMemcachedResource')->willReturn($memcachedResource);

        $this->assertTrue($memcached->has('foo'));
    }

    public function testSet()
    {
        $expiration = time();

        $memcachedResource = $this->getMock('memcachedResource', ['set']);
        $memcachedResource->expects($this->once())->method('set')->with('foo', 'bar', $expiration);

        $memcached = $this->getMock('Sloths\Cache\Storage\Memcached', ['getMemcachedResource']);
        $memcached->expects($this->once())->method('getMemcachedResource')->willReturn($memcachedResource);

        $memcached->set('foo', 'bar', $expiration);
    }

    public function testRemove()
    {
        $memcachedResource = $this->getMock('memcachedResource', ['delete']);
        $memcachedResource->expects($this->once())->method('delete')->with('foo');

        $memcached = $this->getMock('Sloths\Cache\Storage\Memcached', ['getMemcachedResource']);
        $memcached->expects($this->once())->method('getMemcachedResource')->willReturn($memcachedResource);

        $memcached->remove('foo');
    }

    public function testRemoveAll()
    {
        $memcachedResource = $this->getMock('memcachedResource', ['flush']);
        $memcachedResource->expects($this->once())->method('flush');

        $memcached = $this->getMock('Sloths\Cache\Storage\Memcached', ['getMemcachedResource']);
        $memcached->expects($this->once())->method('getMemcachedResource')->willReturn($memcachedResource);

        $memcached->removeAll();
    }

    public function testReplace()
    {
        $memcachedResource = $this->getMock('memcachedResource', ['replace']);
        $memcachedResource->expects($this->once())->method('replace')->with('foo', 'bar');

        $memcached = $this->getMock('Sloths\Cache\Storage\Memcached', ['getMemcachedResource']);
        $memcached->expects($this->once())->method('getMemcachedResource')->willReturn($memcachedResource);

        $memcached->replace('foo', 'bar');
    }
}