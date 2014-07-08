<?php

namespace SlothsTest\Authentication\Storage;

use Sloths\Authentication\Storage\Session;
use SlothsTest\TestCase;

class SessionTest extends TestCase
{
    public function testInstanceMissingSessionManager()
    {
        $storage = new Session();
        $this->assertInstanceOf('Sloths\Session\Session', $storage->getSessionManager());
    }

    /**
     * @dataProvider dataProvider
     */
    public function test($name)
    {
        $sessionManager = $this->getMock('Sloths\Session\Session', ['has', 'get', 'set', 'remove']);
        $sessionManager->expects($this->once())->method('has')->with($name)->willReturn(true);
        $sessionManager->expects($this->once())->method('get')->with($name)->willReturn('foo');
        $sessionManager->expects($this->once())->method('set')->with($name, 'foo');
        $sessionManager->expects($this->once())->method('remove')->with($name);

        $storage = new Session($sessionManager, $name);
        $this->assertTrue($storage->exists());
        $this->assertSame('foo', $storage->read());
        $this->assertSame($storage, $storage->write('foo'));
        $this->assertSame($storage, $storage->clear());
    }

    public function dataProvider()
    {
        return [
            [Session::DEFAULT_NAME],
            ['foo']
        ];
    }
}