<?php

namespace SlothsTest\Session;

use SlothsTest\TestCase;
use Sloths\Session\Flash;

/**
 * @covers Sloths\Session\Flash
 */
class FlashTest extends TestCase
{
    public function test()
    {
        $data = ['foo' => 'foo'];

        $flash = new Flash($data);
        $this->assertSame('foo', $flash->get('foo'));

        $flash->set('foo', 'bar');
        $this->assertSame('foo', $flash->get('foo'));

        $flash->now();
        $this->assertSame('bar', $flash->get('foo'));

        $this->assertNull($flash->get('bar'));
    }

    public function testKeep()
    {
        $data = ['foo' => 'bar'];
        $flash = new Flash($data);

        $this->assertSame([], $data);

        $flash->keep();
        $this->assertSame(['foo' => 'bar'], $data);

    }
}