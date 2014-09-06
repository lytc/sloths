<?php

namespace SlothsTest\Application\Service;

use Sloths\Application\Service\ServiceInterface;
use Sloths\Application\Service\ServiceTrait;

/**
 * @covers Sloths\Application\Service\ServiceTrait
 */
class ServiceTraitTest extends TestCase
{
    public function testSetApplication()
    {
        $service = new BarService();
        $service->setName('foo');

        $configLoader = $this->getMock('ConfigLoader', ['apply']);
        $configLoader->expects($this->once())->method('apply')->with('foo', $service);
        $application = $this->getMock('Sloths\Application\ApplicationInterface');
        $application->expects($this->once())->method('getConfigLoader')->willReturn($configLoader);

        $service->setApplication($application);
        $service->setApplication($application);
    }

}

class BarService implements ServiceInterface
{
    use ServiceTrait;
}