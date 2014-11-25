<?php

namespace SlothsTest\Authentication;

use Sloths\Authentication\Result;
use SlothsTest\TestCase;
use Sloths\Authentication\Authenticator;

/**
 * @covers Sloths\Authentication\Authenticator
 */
class AuthenticatorTest extends TestCase
{
    public function testAuthenticateSuccess()
    {
        $data = 'user data';
        $result = new Result(Result::SUCCESS, $data);

        $adapter = $this->getMock('Sloths\Authentication\Adapter\AdapterInterface');
        $adapter->expects($this->once())->method('setIdentity')->with('username');
        $adapter->expects($this->once())->method('setCredential')->with('pass');
        $adapter->expects($this->once())->method('authenticate')->willReturn($result);


        $storage = $this->getMock('Sloths\Authentication\Storage\StorageInterface');
        $storage->expects($this->once())->method('write')->with($data);
        $storage->expects($this->once())->method('exists')->willReturn(true);
        $storage->expects($this->once())->method('read')->willReturn($data);
        $storage->expects($this->once())->method('clear');

        $authenticator = new Authenticator();
        $authenticator->setAdapter($adapter);
        $authenticator->setStorage($storage);

        $this->assertSame($result, $authenticator->authenticate('username', 'pass'));

        $this->assertTrue($authenticator->exists());
        $this->assertSame($data, $authenticator->getData());
        $this->assertSame($authenticator, $authenticator->clear());

    }

    public function testAuthenticateFail()
    {
        $result = new Result(Result::ERROR_IDENTITY_NOT_FOUND);

        $adapter = $this->getMock('Sloths\Authentication\Adapter\AdapterInterface');
        $adapter->expects($this->once())->method('setIdentity')->with('username');
        $adapter->expects($this->once())->method('setCredential')->with('pass');
        $adapter->expects($this->once())->method('authenticate')->willReturn($result);


        $storage = $this->getMock('Sloths\Authentication\Storage\StorageInterface');
        $storage->expects($this->never())->method('write');

        $authenticator = new Authenticator();
        $authenticator->setAdapter($adapter);
        $authenticator->setStorage($storage);

        $this->assertSame($result, $authenticator->authenticate('username', 'pass'));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetAdapterShouldThrowAnExceptionIfHaveNoAdapter()
    {
        $authenticator = new Authenticator();
        $authenticator->getAdapter();
    }

    public function testDefaultStorage()
    {
        $authentication = new Authenticator();
        $this->assertInstanceOf('Sloths\Authentication\Storage\Session', $authentication->getStorage());
    }
}