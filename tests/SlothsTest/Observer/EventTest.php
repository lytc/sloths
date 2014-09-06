<?php

namespace SlothsTest\Observer;

use Sloths\Observer\Callback;
use Sloths\Observer\Event;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Observer\Event
 */
class EventTest extends TestCase
{
    public function test()
    {
        $event = new Event('foo');
        $this->assertSame('foo', $event->getName());

        $event->addCallback(function() {});
        $event->addCallback(new Callback(function() {}));
        $this->assertCount(2, $event->getCallbacks());
    }

    public function testCallReturnsTheLastResult()
    {
        $event = new Event('foo');
        $event->addCallback(function() use (&$callback1Called) {
            $callback1Called = true;
        });
        $event->addCallback(function($event, $foo) {
            return $foo;
        });

        $this->assertSame('foo', $event->call(['foo']));
        $this->assertTrue($callback1Called);
    }

    public function testCallShouldRemoveCallbackIfLimitExceeded()
    {
        $event = new Event('foo');
        $event->addCallback(function() {

        }, 1);
        $this->assertCount(1, $event->getCallbacks());
        $event->call();
        $event->call();

        $this->assertCount(0, $event->getCallbacks());
    }

    public function testStop()
    {
        $event = new Event('foo');

        $event->addCallback(function($event) {
            $event->stop();
            return 1;
        });

        $event->addCallback(function() {
            return 2;
        });

        $this->assertSame(1, $event->call());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddInvalidCallbackShouldThrowAnException()
    {
        $event = new Event('foo');
        $event->addCallback('foo');
    }
}