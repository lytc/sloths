<?php

namespace SlothsTest\Authentication\Adapter;
use Sloths\Authentication\Adapter\AbstractAdapter;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Authentication\Adapter\AbstractAdapter
 */
class AbstractAdapterTest extends TestCase
{
    public function test()
    {
        $adapter = $this->getMockForAbstractClass('Sloths\Authentication\Adapter\AbstractAdapter');
        $adapter->setIdentity('foo')->setCredential('bar');

        $this->assertSame('foo', $adapter->getIdentity());
        $this->assertSame('bar', $adapter->getCredential());
    }

    public function testCustomVerifier()
    {
        $adapter = new FooAdapter();
        $adapter->setCredentialVerifier(function($credential, $hash) {
            return md5($credential) == $hash;
        });
        $adapter->setCredential('test');
        $this->assertTrue($adapter->authenticate());

        $adapter->setCredential('foo');
        $this->assertFalse($adapter->authenticate());
    }

    /**
     * @dataProvider dataProviderVerifier
     */
    public function testVerifier($name, $className)
    {
        $adapter = $this->getMockForAbstractClass('Sloths\Authentication\Adapter\AbstractAdapter');
        $adapter->setCredentialVerifier($name);
        $this->assertInstanceOf($className, $adapter->getCredentialVerifier());
    }

    public function dataProviderVerifier()
    {
        return [
            ['md5', 'Sloths\Authentication\Verifier\Md5'],
            ['password', 'Sloths\Authentication\Verifier\Password'],
            [function() {}, 'Sloths\Authentication\Verifier\Callback']
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidUndefinedVerifierShouldThrowAnException()
    {
        $adapter = $this->getMockForAbstractClass('Sloths\Authentication\Adapter\AbstractAdapter');
        $adapter->setCredentialVerifier('foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidVerifierShouldThrowAnException()
    {
        $adapter = $this->getMockForAbstractClass('Sloths\Authentication\Adapter\AbstractAdapter');
        $adapter->setCredentialVerifier(new \stdClass());
    }
}

class FooAdapter extends AbstractAdapter
{
    public function authenticate()
    {
        return $this->verifyCredential(md5('test'));
    }
}