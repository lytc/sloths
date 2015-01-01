<?php

namespace SlothsTest\Application\Service;

use Sloths\Application\Service\Password;

class PasswordTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $adapter = $this->getMock('Sloths\Encryption\Password\PasswordInterface');
        $adapter->expects($this->once())->method('hash')->with('foo')->willReturn('bar');
        $adapter->expects($this->once())->method('verify')->with('foo', 'bar')->willReturn(true);
        $adapter->expects($this->once())->method('needsRehash')->with('foo')->willReturn(true);

        $password = new Password();
        $password->setAdapter($adapter);

        $this->assertSame('bar', $password->hash('foo'));
        $this->assertTrue($password->verify('foo', 'bar'));
        $this->assertTrue($password->needsRehash('foo'));
    }
}