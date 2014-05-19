<?php

namespace SlothsTest\Application;

use Sloths\Application\Application;
use Sloths\Application\Service\ServiceInterface;
use Sloths\Application\Service\ServiceTrait;
use SlothsTest\TestCase;

@session_start();
class ServiceTest extends TestCase
{
    /**
     * @dataProvider serviceProvider
     */
    public function testAddService($service)
    {
        $application = new Application();
        $application->addService('foo', $service);
        $application->addServices(['bar' => $service]);

        $this->assertTrue($application->hasService('foo'));
        $this->assertInstanceOf(__NAMESPACE__ . '\FooService', $application->getService('foo'));
        $this->assertInstanceOf(__NAMESPACE__ . '\FooService', $application->foo);
        $this->assertSame($application->getService('foo'), $application->foo);
        $this->assertSame($application, $application->foo->getApplication());

        $this->assertTrue($application->hasService('bar'));
        $this->assertInstanceOf(__NAMESPACE__ . '\FooService', $application->getService('bar'));
        $this->assertInstanceOf(__NAMESPACE__ . '\FooService', $application->bar);
        $this->assertSame($application->getService('bar'), $application->bar);
        $this->assertSame($application, $application->bar->getApplication());
    }

    public function testSetService()
    {
        $service1 = new FooService();
        $service2 = new FooService();
        $service3 = new FooService();

        $application = new Application();

        $application->setService('foo', $service1);
        $this->assertSame($service1, $application->getService('foo'));

        $application->setService('foo', $service2);
        $this->assertSame($service2, $application->getService('foo'));

        $application->setServices(['foo' => $service3]);
        $this->assertSame($service3, $application->getService('foo'));
    }

    public function serviceProvider()
    {
        return [
            [__NAMESPACE__ . '\FooService'],
            [new FooService()],
            [function() { return new FooService();}]
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddUnExistingServiceClassShouldThrowAnException()
    {
        $application = new Application();
        $application->addService('foo', 'non_existing_service_class');
        $application->foo;
    }

    /**
     * @dataProvider invalidServiceProvider
     * @expectedException \InvalidArgumentException
     */
    public function testAddInvalidServiceShouldThrowAnException($service)
    {
        $application = new Application();
        $application->addService('foo', $service);
        $application->getService('foo');
    }

    public function invalidServiceProvider()
    {
        return [
            ['stdClass'],
            [new \stdClass()],
            [function() {return new \stdClass();}]
        ];
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testAddDuplicateServiceShouldThrowAnException()
    {
        $application = $this->mock('Sloths\Application\Application[hasService]');
        $application->shouldReceive('hasService')->once()->with('foo')->andReturn(true);

        $application->addService('foo', 'foo');
    }

    /**
     * @dataProvider defaultServiceProvider
     */
    public function testDefaultService($serviceName, $serviceClassName)
    {
        $application = new Application();
        $this->assertInstanceOf($serviceClassName, $application->getService($serviceName));
        $this->assertInstanceOf($serviceClassName, $application->$serviceName);
    }

    public function defaultServiceProvider()
    {
        $reflection = new \ReflectionProperty('Sloths\Application\Application', 'services');
        $reflection->setAccessible(true);
        $services = $reflection->getValue(new Application());

        $result = [];
        foreach ($services as $name => $className) {
            $result[] = [$name, $className];
        }

        return $result;
    }
}

class FooService implements ServiceInterface
{
    use ServiceTrait;
}