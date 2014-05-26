<?php

namespace SlothsTest\Session;

use Sloths\Session\Session;
use SlothsTest\TestCase;

@session_start();

/**
 * @covers \Sloths\Session\Session
 */
class SessionTest extends TestCase
{
    public function testSetAndGetNamespace()
    {
        $session = new Session();
        $session->setNamespace('foo');
        $this->assertSame('foo', $session->getNamespace());
    }

    public function testArrayAccess()
    {
        $session = new Session(null, ['ns' => ['foo' => 1]]);
        $session->setNamespace('ns');

        $this->assertTrue(isset($session['foo']));
        $this->assertSame(1, $session['foo']);
        $this->assertFalse(isset($session['bar']));
        $this->assertNull($session['bar']);

        $session['bar'] = 2;
        $this->assertSame(2, $session['bar']);

        unset($session['bar']);
        $this->assertFalse(isset($session['bar']));
    }

    public function testGetterAndSetter()
    {
        $session = new Session(null, ['ns' => ['foo' => 1]]);
        $session->setNamespace('ns');
        $this->assertSame(1, $session->foo);
        $this->assertNull($session->bar);

        $session->bar = 2;
        $this->assertTrue($session->has('bar'));
        $this->assertSame(2, $session->bar);

        unset($session['bar']);
        $this->assertFalse($session->has('bar'));
    }

    public function testRemoveAndUnset()
    {
        $session = new Session(null, ['ns' => ['foo' => 1, 'bar' => 2]]);
        $session->setNamespace('ns');
        $this->assertSame(1, $session->foo);
        $this->assertSame(2, $session->bar);

        $session->remove('foo');
        unset($session['bar']);

        $this->assertFalse($session->has('foo'));
        $this->assertFalse($session->has('bar'));
    }

    public function testGetContainer()
    {
        $storage = ['ns' => ['foo' => 1]];
        $session = new Session(null, $storage);
        $session->setNamespace('ns');

        $this->assertSame(['foo' => 1], $session->getContainer());
    }

    public function testClear()
    {
        $storage = ['ns' => ['foo' => 1]];
        $session = new Session(null, $storage);
        $session->setNamespace('ns');

        $session->clear();
        $this->assertSame([], $session->getContainer());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSetNameShouldThrowAnExceptionIfSessionAlreadyStarted()
    {
        $session = $this->getMock('Sloths\Session\Session', ['isActive']);
        $session->expects($this->once())->method('isActive')->willReturn(true);

        $session->setName('foo');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSetIdShouldThrowAnExceptionIfSessionAlreadyStarted()
    {
        $session = $this->getMock('Sloths\Session\Session', ['isActive']);
        $session->expects($this->once())->method('isActive')->willReturn(true);

        $session->setId('foo');
    }
}