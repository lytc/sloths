<?php

namespace SlothsTest\Session;

use Sloths\Session\Flash;
use Sloths\Session\Session;
use SlothsTest\TestCase;

@session_start();

class FlashTest extends TestCase
{
    public function test()
    {
        $session = new Session(null, []);
        $flash = new Flash('flash', $session);
        $this->assertFalse($flash->has('foo'));

        $flash['foo'] = 1;
        $this->assertFalse($flash->has('foo'));

        $next = new Flash('flash', $session);
        $this->assertSame(1, $next['foo']);

        $next = new Flash('flash', $session);
        $this->assertFalse($next->has('foo'));
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
}