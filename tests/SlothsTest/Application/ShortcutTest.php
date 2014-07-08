<?php

namespace SlothsTest\Application;

use Sloths\Application\Application;
use Sloths\Application\Service\ServiceInterface;
use Sloths\Application\Service\ServiceTrait;
use SlothsTest\TestCase;

/**
 * @covers \Sloths\Application\Application
 */
class ShortcutTest extends TestCase
{
    public function testAddShortcutMethod()
    {
        $service = $this->getMock(__NAMESPACE__ . '\BarService', ['foo', 'bar', 'baz', 'qux']);
        $service->expects($this->once())->method('foo')->with('foo')->willReturn(1);
        $service->expects($this->once())->method('bar')->with('bar')->willReturn(2);
        $service->expects($this->once())->method('baz')->with('baz')->willReturn(3);
        $service->expects($this->once())->method('qux')->with('qux')->willReturn(4);

        $application = new Application();
        $application->addService('foo', $service);
        $application->addShortcutMethod('foo', 'foo');
        $application->addShortcutMethods(['bar' => 'foo', 'baz' => ['foo'], 'wot' => ['foo', 'qux']]);
        $this->assertSame(1, $application->foo('foo'));
        $this->assertSame(2, $application->bar('bar'));
        $this->assertSame(3, $application->baz('baz'));
        $this->assertSame(4, $application->wot('qux'));
    }

    public function testSetShortcutMethod()
    {
        $service = $this->getMock(__NAMESPACE__ . '\BarService', ['foo', 'bar']);
        $service->expects($this->once())->method('foo')->with('foo')->willReturn(1);
        $service->expects($this->exactly(2))->method('bar')->with('foo')->willReturn(2);

        $application = new Application();
        $application->addService('service', $service);
        $application->setShortcutMethod('foo', 'service');
        $this->assertSame(1, $application->foo('foo'));

        $application->setShortcutMethods(['foo' => ['service', 'bar'], 'bar' => 'service']);
        $this->assertSame(2, $application->foo('foo'));
        $this->assertSame(2, $application->bar('foo'));
    }

    public function testAddShortcutProperty()
    {
        $service = new BarService();
        $application = new Application();
        $application->addService('service', $service);
        $application->addShortcutProperty('foo', 'service');
        $application->addShortcutProperties(['bar' => 'service', 'qux' => ['service', 'baz']]);
        $this->assertSame($application->foo, $service->foo);
        $this->assertSame($application->bar, $service->bar);
        $this->assertSame($application->qux, $service->baz);
    }

    public function testSetShortcutProperty()
    {
        $service = new BarService();
        $application = new Application();
        $application->addService('service', $service);

        $application->setShortcutProperty('foo', 'service');
        $this->assertSame($application->foo, $service->foo);

        $application->setShortcutProperties(['foo' => ['service', 'bar'], 'bar' => 'service']);
        $this->assertSame($application->foo, $service->bar);
        $this->assertSame($application->bar, $service->bar);
    }

    /**
     * @dataProvider defaultShortcutMethodProvider
     */
    public function testDefaultShortcutMethods($method, $serviceName, $originMethod)
    {
        $application = new Application();
        $service = $this->getMock('Sloths\Application\Service\ServiceInterface', ['setApplication', 'getApplication', $originMethod]);
        $service->expects($this->once())->method($originMethod);

        $application->setService($serviceName, $service);

        $application->{$method}();
    }

    public function defaultShortcutMethodProvider()
    {
        $reflection = new \ReflectionProperty('Sloths\Application\Application', 'shortcutMethods');
        $reflection->setAccessible(true);
        $methods = $reflection->getValue(new Application());

        $result = [];
        foreach ($methods as $name => $meta) {
            $result[] = [$name, $meta[0], $meta[1]];
        }

        return $result;
    }

    /**
     * @dataProvider defaultShortcutPropertyProvider
     */
    public function testDefaultShortcutProperties($name, $serviceName, $originProperty)
    {
        $application = new Application();
        $this->assertSame($application->$name, $application->$serviceName->$originProperty);
    }

    public function defaultShortcutPropertyProvider()
    {
        $reflection = new \ReflectionProperty('Sloths\Application\Application', 'shortcutProperties');
        $reflection->setAccessible(true);
        $properties = $reflection->getValue(new Application());

        $result = [];
        foreach ($properties as $name => $meta) {
            $result[] = [$name, $meta[0], $meta[1]];
        }

        return $result;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddDuplicateShortcutMethodShouldThrowAnException()
    {
        $application = $this->getMock('Sloths\Application\Application', ['hasShortcutMethod']);
        $application->expects($this->once())->method('hasShortcutMethod')->with('foo')->willReturn(true);

        $application->addShortcutMethod('foo', 'foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddDuplicateShortcutPropertyShouldThrowAnException()
    {
        $application = $this->getMock('Sloths\Application\Application', ['hasShortcutProperty']);
        $application->expects($this->once())->method('hasShortcutProperty')->with('foo')->willReturn(true);

        $application->addShortcutProperty('foo', 'foo');
    }
}


class BarService implements ServiceInterface
{
    use ServiceTrait;

    public $foo = 'foo';
    public $bar = 'bar';
    public $baz = 'baz';
}