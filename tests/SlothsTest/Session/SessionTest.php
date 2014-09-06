<?php

namespace SlothsTest\Session;

use Sloths\Session\Container;
use SlothsTest\TestCase;

use Sloths\Session\Session;

/**
 * @covers Sloths\Session\Session
 */
class SessionTest extends TestCase
{
    public function test()
    {
        $data = ['foo' => 'foo'];
        $container = new Container($data);

        $adapter = $this->getMock('Sloths\Session\Adapter\AdapterInterface');
        $adapter->expects($this->any())->method('getContainer')->willReturn($container);

        $session = new Session($adapter);

        $this->assertSame('foo', $session->get('foo'));

        $foo = &$session->get('foo');
        $foo = 'bar';
        $this->assertSame('bar', $session->get('foo'));

        $session->set('foo', 'baz');
        $this->assertSame('baz', $session->get('foo'));
        $this->assertTrue($session->has('foo'));

        $session->remove('foo');
        $this->assertNull($session->get('foo'));
        $this->assertFalse($session->has('foo'));

        $flash = $session->flash();
        $this->assertInstanceOf('Sloths\Session\Flash', $flash);
        $this->assertSame($flash, $session->flash());
    }

    public function testDefaultAdapter()
    {
        $session = new Session();
        $this->assertInstanceOf('Sloths\Session\Adapter\Native', $session->getAdapter());
    }
}