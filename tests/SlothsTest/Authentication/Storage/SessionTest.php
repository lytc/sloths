<?php

namespace SlothsTest\Authentication\Storage;

use Sloths\Authentication\Storage\Session;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Authentication\Storage\Session
 */
class SessionTest extends TestCase
{
    public function test()
    {
        $sessionManager = $this->getMock('Sloths\Session\Session', ['has', 'get', 'set', 'remove']);
        $sessionManager->expects($this->once())->method('has')->willReturn(true);
        $sessionManager->expects($this->once())->method('get')->with(Session::DEFAULT_NAME)->willReturn('data');
        $sessionManager->expects($this->once())->method('set')->with(Session::DEFAULT_NAME, 'new data');
        $sessionManager->expects($this->once())->method('remove')->with(Session::DEFAULT_NAME);

        $storage = new Session($sessionManager);

        $this->assertTrue($storage->exists());
        $this->assertSame('data', $storage->read());
        $storage->write('new data');
        $storage->clear();
    }

    public function testDefaultSessionManager()
    {
        $storage = new Session();
        $this->assertInstanceOf('Sloths\Session\Session', $storage->getSessionManager());
    }
}