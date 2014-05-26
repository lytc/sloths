<?php

namespace SlothsTest\Observer;

use Sloths\Observer\Observer;

/**
 * @covers \Sloths\Observer\ObserverTrait
 */
class ObserverTest extends \PHPUnit_Framework_TestCase
{
    public function testAddAndRemoveListener()
    {
        $observer = new Observer();

        $observer->addListener('foo', function() {});
        $this->assertCount(1, $observer->getListener('foo'));

        $callback = function() {};
        $observer->addListener('foo', $callback);
        $this->assertCount(2, $observer->getListener('foo'));

        $observer->removeListener('foo', $callback);
        $this->assertCount(1, $observer->getListener('foo'));

        $observer->removeListener('foo');
        $this->assertCount(0, $observer->getListener('foo'));

        $observer->removeListener();
        $this->assertCount(0, $observer->getListeners());
    }

    public function testHasListener()
    {
        $observer = new Observer();
        $callback1 = function() {};
        $callback2 = function() {};

        $observer->addListener('foo', $callback1);
        $observer->addListener('foo', $callback2);
        $this->assertTrue($observer->hasListener('foo', $callback1));
        $this->assertTrue($observer->hasListener('foo'));

        $observer->removeListener('foo', $callback1);
        $this->assertFalse($observer->hasListener('foo', $callback1));
        $this->assertTrue($observer->hasListener('foo', $callback2));
        $this->assertTrue($observer->hasListener());

        $this->assertFalse($observer->hasListener('bar'));
    }

    public function testNotify()
    {
        $args = [1, 2];
        $calledCount = 0;
        $observer = new Observer();
        $callback1 = function() use (&$scope, &$calledCount, &$callback1CalledWithArgs) {
            $scope = $this;
            $args = func_get_args();
            $callback1CalledWithArgs = $args;
            $calledCount++;
        };
        $callback2 = function() use (&$callback2CalledWithArgs) {
            $callback2CalledWithArgs = func_get_args();
        };

        $observer->addListener('foo', $callback1);
        $observer->addListener('bar', $callback2);

        $observer->notify('foo', $args);
        $this->assertSame($observer, $scope);
        $this->assertSame($args, $callback1CalledWithArgs);
        $this->assertSame(1, $calledCount);

        $observer->notify('foo');
        $this->assertSame(2, $calledCount);

        $observer->removeListener('foo', $callback1);
        $observer->notify('foo');
        $this->assertSame(2, $calledCount);
    }

    public function testItShouldStopWhenCallbackReturnsFalse()
    {
        $count = 0;
        $callback1 = function() use (&$count) {$count++;};
        $callback2 = function() use (&$count) {$count++; return false;};
        $callback3 = function() use (&$count) {$count++;};

        $observer = new Observer();
        $observer->addListener('foo', $callback1);
        $observer->addListener('foo', $callback2);
        $observer->addListener('foo', $callback3);

        $observer->notify('foo');
        $this->assertSame(2, $count);
    }

    public function testAddListenerOne()
    {
        $count = 0;
        $callback = function() use (&$count) {$count++;};
        $observer = new Observer();
        $observer->addListenerOne('foo', $callback);
        $observer->notify('foo');
        $observer->notify('foo');

        $this->assertSame(1, $count);
    }

    public function testListenTo()
    {
        $observer1 = new Observer();
        $observer2 = new Observer();
        $callback = function() use (&$count, &$scope) {
            $count++;
            $scope = $this;
        };

        $count = 0;
        $observer2->listenTo($observer1, 'foo', $callback);

        $observer1->notify('foo');
        $this->assertSame(1, $count);
        $this->assertSame($observer2, $scope);

        $observer1->notify('foo');
        $this->assertSame(2, $count);

        $observer2->stopListening($observer1, 'foo', $callback);
        $observer1->notify('foo');
        $this->assertSame(2, $count);

        $observer2->listenTo($observer1, 'foo', $callback);
        $observer2->stopListening();
        $observer1->notify('foo');
        $this->assertSame(2, $count);
    }

    public function testListenOneTo()
    {
        $observer1 = new Observer();
        $observer2 = new Observer();
        $callback = function() use (&$count, &$scope) {
            $count++;
            $scope = $this;
        };

        $count = 0;
        $observer2->listenOneTo($observer1, 'foo', $callback);

        $observer1->notify('foo');
        $this->assertSame(1, $count);
        $this->assertSame($observer2, $scope);

        $observer1->notify('foo');
        $this->assertSame(1, $count);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage foo: Event not found
     */
    public function testRemoveUnregisteredEventShouldThrowAnException()
    {
        $observer = new Observer();
        $observer->removeListener('foo');
    }
}