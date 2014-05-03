<?php

namespace LazyTest\Config;

use Lazy\Config\Config;
use LazyTest\TestCase;

class ConfigTest extends TestCase
{
    public function testSetAndGet()
    {
        $config = new Config();
        $this->assertNull($config->foo);

        $config->foo = 'foo';
        $this->assertSame('foo', $config->foo);

        $config->bar = ['baz' => 'qux'];
        $this->assertInstanceOf('Lazy\Config\Config', $config->bar);
        $this->assertSame('qux', $config->bar->baz);
    }

    public function testCount()
    {
        $config = new Config();
        $this->assertCount(0, $config);

        $config->foo = 'bar';
        $this->assertCount(1, $config);
    }

    public function testGetIterator()
    {
        $config = new Config();
        $this->assertInstanceOf('Generator', $config->getIterator());
    }

    public function testMerge()
    {
        $config = new Config(['foo' => 'foo']);
        $config->merge(new Config(['foo' => 'bar', 'bar' => 'baz']));

        $this->assertSame(['foo' => 'bar', 'bar' => 'baz'], $config->toArray());
    }

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

    public function testNested()
    {
        $config = new Config([
            'foo' => [
                'bar' => 'baz'
            ]
        ]);

        $this->assertInstanceOf('Lazy\Config\Config', $config->foo);
        $this->assertSame('baz', $config->foo->bar);
    }

    public function testReplaceRecursive()
    {
        $config = new Config([
            'foo' => [
                'bar' => 'baz',
                'baz' => 'qux'
            ]
        ]);

        $config->replaceRecursive(new Config([
            'foo' => [
                'baz' => 'wot'
            ]
        ]));

        $this->assertSame('baz', $config->foo->bar);
        $this->assertSame('wot', $config->foo->baz);
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