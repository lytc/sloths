<?php

namespace SlothsTest\Application;

use Sloths\Misc\ConfigurableInterface;
use Sloths\Misc\ConfigurableTrait;
use SlothsTest\TestCase;
use Sloths\Application\ConfigLoader;

/**
 * @covers Sloths\Application\ConfigLoader
 */
class ConfigLoaderTest extends TestCase
{
    public function testApply()
    {
        $application = $this->getMock('Sloths\Application\ApplicationInterface');
        $application->expects($this->once())->method('getEnv')->willReturn('development');

        $configLoader = new ConfigLoader($application);
        $configLoader->addDirectories(__DIR__ . '/fixtures/config-loader');

        $foo = new Foo();
        $configLoader->apply('foo', $foo);

        $this->assertTrue($foo->fromGlobalLoaded);
        $this->assertTrue($foo->fromLocalLoaded);
    }
}

class Foo implements ConfigurableInterface
{
    use ConfigurableTrait;

    public $fromGlobalLoaded = false;
    public $fromLocalLoaded = false;
}