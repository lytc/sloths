<?php

namespace SlothsTest\Session;

use Sloths\Session\Flash;
use Sloths\Session\Session;
use SlothsTest\TestCase;

@session_start();

/**
 * @covers \Sloths\Session\Flash
 */
class FlashTest extends TestCase
{
    public function test()
    {
        $session = new Session(null, []);
        $flash = new Flash('flash', $session);
        $this->assertFalse($flash->has('foo'));

        $flash['foo'] = 1;
        $this->assertFalse($flash->has('foo'));
        $this->assertSame(['foo' => 1], $flash->getNextData());
        foreach ($flash as $k => $v) {
            $this->assertSame('foo', $k);
            $this->assertSame(1, $v);
        }

        $next = new Flash('flash', $session);
        $this->assertSame(1, $next['foo']);

        $next = new Flash('flash', $session);
        $this->assertFalse($next->has('foo'));

        $flash->set('foo', 1);
        $this->assertSame(1, $flash->getNextData()['foo']);

        $flash->set(null, 'bar');
        $this->assertSame('bar', $flash->getNextData()[0]);

        $flash->clear();
        $this->assertSame([], $flash->getNextData());
    }

    public function testRemoveAndUnset()
    {
        $session = new Session(null, []);
        $flash = new Flash('flash', $session);
        $flash['foo'] = 1;
        $flash['bar'] = 2;
        $flash['baz'] = 3;

        $flash->remove('foo');
        unset($flash['bar']);

        $next = new Flash('flash', $session);

        $this->assertFalse($next->has('foo'));
        $this->assertFalse($next->has('bar'));
        $this->assertSame(3, $next['baz']);
    }

    public function testKeep()
    {
        $session = new Session(null, []);
        $flash = new Flash('flash', $session);
        $flash['foo'] = 1;

        $next = new Flash('flash', $session);
        $this->assertSame(1, $next->foo);
        $next->keep();

        $next = new Flash('flash', $session);
        $this->assertSame(1, $next->foo);
    }

    public function testNow()
    {
        $session = new Session(null, []);
        $flash = new Flash('flash', $session);
        $flash['foo'] = 1;

        $this->assertFalse($flash->has('foo'));
        $flash->now();

        $this->assertSame(1, $flash->foo);
    }

    public function testMagicMethods()
    {
        $session = new Session(null, [Session::DEFAULT_NAMESPACE => ['flash' => ['foo' => 2]]]);
        $flash = new Flash('flash', $session);

        $this->assertSame(['foo' => 2], $flash->getCurrentData());
        $this->assertCount(1, $flash);

        $flash->foo = 1;
        $this->assertSame(['foo' => 1], $flash->getNextData());
        $this->assertTrue(isset($flash['foo']));
        $this->assertSame(2, $flash->foo);
        unset($flash->foo);

        $this->assertSame([], $flash->getNextData());
    }
}