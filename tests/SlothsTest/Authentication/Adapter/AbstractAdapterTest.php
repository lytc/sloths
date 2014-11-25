<?php

namespace SlothsTest\Authentication\Adapter;

use SlothsTest\TestCase;

/**
 * @covers Sloths\Authentication\Adapter\AbstractAdapter
 */
class AbstractAdapterTest extends TestCase
{
    public function test()
    {
        $adapter = $this->getMockForAbstractClass('Sloths\Authentication\Adapter\AbstractAdapter');
        $adapter->setIdentity('username');
        $adapter->setCredential('pass');

        $this->assertSame('username', $adapter->getIdentity());
        $this->assertSame('pass', $adapter->getCredential());
    }
}