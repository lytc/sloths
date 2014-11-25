<?php

namespace SlothsTest\Session;

use Sloths\Session\Container;
use SlothsTest\TestCase;


/**
 * @covers Sloths\Session\Container
 */
class ContainerTest extends TestCase
{
    public function test()
    {
        $data = ['foo' => 'foo'];
        $container = new Container($data);
        $container->set('foo', 'bar');
        $container->set('bar', 'baz');
        $container->set('baz', 'qux');
        $container->remove('bar');

        $this->assertSame($data, $container->getData());
        $this->assertSame(['foo' => 'bar', 'baz' => 'qux'], $data);
        $this->assertSame('bar', $container->get('foo'));
        $this->assertNull($container->get('bar'));
    }
}