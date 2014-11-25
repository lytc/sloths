<?php

namespace SlothsTest\Application\Service;

use Sloths\Application\Application;
use Sloths\Application\Service\AbstractService;
use Sloths\Application\Service\ServiceManager;

/**
 * @covers Sloths\Application\Service\ServiceManager
 */
class ServiceManagerTest extends TestCase
{
    public function testSetServices()
    {
        $manager = $this->getMock('Sloths\Application\Service\ServiceManager', ['add'], [], '', false);
        $manager->expects($this->once())->method('add')->with('foo', 'bar');

        $manager->setServices(['foo' => 'bar']);
    }

    public function testAdd()
    {
        $manager = $this->getMock('Sloths\Application\Service\ServiceManager', ['__construct'], [], '', false);
        $manager->add('foo', 'bar');
        $this->assertTrue($manager->has('foo'));
    }

    public function testGet()
    {
        $application = new Application();
        $manager = new ServiceManager($application);
        $manager->add('foo', __NAMESPACE__ . '\FooService');

        $service = $manager->get('foo');
        $this->assertSame('foo', $service->getName());
        $this->assertSame($application, $service->getApplication());
        $this->assertSame($service, $manager->get('foo'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetUndefinedServiceShouldThrowAnException()
    {
        $manager = $this->getMock('Sloths\Application\Service\ServiceManager', ['__construct'], [], '', false);
        $manager->get('foo');
    }

    /**
     * @expectedException \UnexpectedValueException
     * @dataProvider dataProviderTestNonInstanceOfServiceInterfaceShouldThrowAnException
     */
    public function testNonInstanceOfServiceInterfaceShouldThrowAnException($value)
    {
        $manager = $this->getMock('Sloths\Application\Service\ServiceManager', ['__construct'], [], '', false);
        $manager->add('foo', $value);
        $manager->get('foo');
    }

    public function dataProviderTestNonInstanceOfServiceInterfaceShouldThrowAnException()
    {
        return [
            [function() { return new \stdClass(); }],
            [function() { return []; }],
        ];
    }
}

class FooService extends AbstractService
{

}