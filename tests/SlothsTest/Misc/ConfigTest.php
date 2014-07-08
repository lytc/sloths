<?php

namespace SlothsTest\Misc;

use Sloths\Misc\Config;
use SlothsTest\TestCase;

/**
 * @covers \Sloths\Misc\Config
 */
class ConfigTest extends TestCase
{
    public function testFromFile()
    {
        $config = Config::fromFile(__DIR__ . '/fixtures/config1.php');
        $this->assertSame(['foo' => 'foo', 'bar' => ['baz' => 'baz', 'qux' => 'qux']], $config->toArray());

        $config = Config::fromFile([__DIR__ . '/fixtures/config1.php', __DIR__ . '/fixtures/config2.php']);
        $this->assertSame(['foo' => 'foo', 'bar' => ['baz' => 'qux', 'qux' => 'qux']], $config->toArray());

        $config = Config::fromFile([__DIR__ . '/fixtures/config1.php', __DIR__ . '/fixtures/config2.php'], false);
        $this->assertSame(['foo' => 'foo', 'bar' => ['baz' => 'qux']], $config->toArray());
    }

    public function testConfigFromJsonFile()
    {
        $config = Config::fromFile(__DIR__ . '/fixtures/config.json');
        $this->assertSame('foo', $config->foo);
        $this->assertSame('baz', $config->bar);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidConfigFileTypeShouldThrowAnException()
    {
        Config::fromFile('foo.txt');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidConfigFileShouldThrowAnException()
    {
        Config::fromFile(__DIR__ . '/fixtures/invalid-config.php');
    }
}