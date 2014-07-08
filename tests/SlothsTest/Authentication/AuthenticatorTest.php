<?php

namespace SlothsTest\Authentication;

use Sloths\Authentication\Authenticator;
use Sloths\Authentication\Result;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Authentication\Authenticator
 */
class AuthenticatorTest extends TestCase
{
    public function testAuthenticateSuccess()
    {
        $result = new Result(Result::SUCCESS, 'foo');

        $adapter = $this->getMock('Sloths\Authentication\Adapter\AdapterInterface', ['authenticate']);
        $adapter->expects($this->once())->method('authenticate')->willReturn($result);

        $storage = $this->getMock('Sloths\Authentication\Storage\StorageInterface', ['exists', 'read', 'clear', 'write']);
        $storage->expects($this->once())->method('exists')->willReturn(true);
        $storage->expects($this->once())->method('read')->willReturn('foo');
        $storage->expects($this->once())->method('clear');
        $storage->expects($this->once())->method('write')->with('foo');

        $authenticator = new Authenticator($adapter, $storage);
        $this->assertSame($result, $authenticator->authenticate());
        $this->assertTrue($authenticator->exists());
        $this->assertSame('foo', $authenticator->getData());
    }

    public function testAuthenticateFailure()
    {
        $result = new Result(Result::ERROR_FAILURE);

        $adapter = $this->getMock('Sloths\Authentication\Adapter\AdapterInterface', ['authenticate']);
        $adapter->expects($this->once())->method('authenticate')->willReturn($result);

        $storage = $this->getMock('Sloths\Authentication\Storage\StorageInterface', ['exists', 'read', 'clear', 'write']);
        $storage->expects($this->once())->method('clear');
        $storage->expects($this->never())->method('write');

        $authenticator = new Authenticator($adapter, $storage);
        $this->assertSame($result, $authenticator->authenticate());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAuthenticateWithoutAdapterShouldThrowAnException()
    {
        $authenticator = new Authenticator();
        $authenticator->authenticate();
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testAdapterAuthenticateReturnInvalidResultShouldThrowAnException()
    {
        $adapter = $this->getMock('Sloths\Authentication\Adapter\AdapterInterface', ['authenticate']);
        $adapter->expects($this->once())->method('authenticate')->willReturn(new \stdClass());

        $authenticator = new Authenticator();
        $authenticator->authenticate($adapter);
    }

    public function testGetStorageShouldReturnInstanceOfStorageIfNoStorageProvide()
    {
        $authenticator = new Authenticator();
        $this->assertInstanceOf('Sloths\Authentication\Storage\Session', $authenticator->getStorage());
    }
}