<?php

namespace SlothsTest\Observer;

use Sloths\Observer\Event;
use Sloths\Observer\ObserverTrait;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Observer\ObserverTrait
 */
class ObserverTraitTest extends TestCase
{
    public function testAdd()
    {
        $foo = new Foo();
        $foo->addEventListener('event1', function() {});
        $foo->addEventListeners(['event2' => function() {}]);

        $this->assertTrue($foo->hasEventListener('event1'));
        $this->assertTrue($foo->hasEventListener('event2'));
        $this->assertFalse($foo->hasEventListener('event3'));
    }

    public function testTrigger()
    {
        $foo = new Foo();
        $this->assertNull($foo->triggerEventListener('event1'));

        $foo->addEventListener('event1', function($event, $foo) {
            return $foo;
        });

        $this->assertSame('foo', $foo->triggerEventListener('event1', ['foo']));
    }

    public function testAddEventListenerOne()
    {
        $calledCount1 = 0;
        $calledCount2 = 0;

        $foo = new Foo();
        $foo->addEventListenerOne('event1', function() use (&$calledCount1) {
            $calledCount1++;
        });

        $foo->addEventListenersOne(['event2' => function() use (&$calledCount2) {
            $calledCount2++;
        }]);

        $foo->triggerEventListener('event1');
        $foo->triggerEventListener('event2');

        $this->assertSame(1, $calledCount1);
        $this->assertSame(1, $calledCount2);
    }

    public function testRemoveEventListener()
    {
        $foo = new Foo();
        $foo->addEventListener('event', $callback1 = function() {});
        $foo->addEventListener('event', function() {});
        $foo->addEventListener('event', function() {});

        $this->assertCount(3, $foo->getEventListener('event')->getCallbacks());

        $foo->removeEventListener('event', $callback1);
        $this->assertCount(2, $foo->getEventListener('event')->getCallbacks());

        $foo->removeEventListener('event');
        $this->assertFalse($foo->hasEventListener('event'));

        $foo->addEventListener('event', function() {});
        $foo->removeEventListener($foo->getEventListener('event'));
        $this->assertFalse($foo->hasEventListener('event'));

        $foo->addEventListener('event', function() {});
        $foo->removeAllEventListeners();

        $this->assertFalse($foo->hasEventListener('event'));
    }
}

class Foo
{
    use ObserverTrait;
}