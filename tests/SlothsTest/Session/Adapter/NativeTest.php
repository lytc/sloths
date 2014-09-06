<?php

namespace SlothsTest\Session\Adapter;

use SlothsTest\TestCase;
use Sloths\Session\Adapter\Native;

/**
 * @covers Sloths\Session\Adapter\Native
 */
class NativeTest extends TestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testSetIdShouldThrowAnExceptionIfSessionAlreadyStarted()
    {
        $adapter = $this->getMock('Sloths\Session\Adapter\Native', ['isStarted']);
        $adapter->expects($this->once())->method('isStarted')->willReturn(true);

        $adapter->setId('foo');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSetNameShouldThrowAnExceptionIfSessionAlreadyStarted()
    {
        $adapter = $this->getMock('Sloths\Session\Adapter\Native', ['isStarted']);
        $adapter->expects($this->once())->method('isStarted')->willReturn(true);

        $adapter->setName('foo');
    }

    public function testGetContainer()
    {
        $adapter = $this->getMock('Sloths\Session\Adapter\Native', ['start']);
        $container = $adapter->getContainer();
        $this->assertInstanceOf('Sloths\Session\Container', $container);
        $this->assertSame($container, $container);
    }
}