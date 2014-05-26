<?php

namespace SlothsTest\Application;

use Sloths\Application\Application;
use SlothsTest\TestCase;

/**
 * @covers \Sloths\Application\Application
 */
class ApplicationConfigTest extends TestCase
{
    public function testLoadApplicationConfig()
    {
        $application = $this->getMock('Sloths\Application\Application', ['notFound']);
        $application->expects($this->once())->method('notFound');

        $application->setDirectory(__DIR__ . '/fixtures');
        $application->setEnv('development');

        $application->run();

        $this->assertInstanceOf('stdClass', $application->foo);
        $this->assertInstanceOf('stdClass', $application->bar);
    }

    public function testLoadServiceConfig()
    {
        $application = new Application();
        $application->setDirectory(__DIR__ . '/fixtures');
        $application->setEnv('development');

        $application->addService('fooService', function() {
            return new \stdClass();
        });

        $this->assertSame('bar', $application->fooService->foo);
        $this->assertSame('baz', $application->fooService->bar);
    }
}