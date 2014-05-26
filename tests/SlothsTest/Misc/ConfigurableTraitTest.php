<?php

namespace SlothsTest\Misc;

use Sloths\Misc\Config;
use Sloths\Misc\ConfigurableTrait;
use SlothsTest\TestCase;

/**
 * @covers \Sloths\Misc\ConfigurableTrait
 */
class ConfigurableTraitTest extends TestCase
{
    public function test()
    {
        $configurable = new ConfigurableMock();
        $this->assertInstanceOf('Sloths\Misc\Config', $configurable->getConfig());

        $config = new Config();
        $configurable->setConfig($config);

        $this->assertSame($config, $configurable->getConfig());
    }
}

class ConfigurableMock
{
    use ConfigurableTrait;
}