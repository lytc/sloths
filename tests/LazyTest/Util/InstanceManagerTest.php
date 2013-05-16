<?php

namespace LazyTest\Util;

use Lazy\Util\InstanceManager;

class InstanceManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        InstanceManager::register('foo', '\Exception', ['foo', 500]);

        $instance = InstanceManager::get('foo');
        $this->assertInstanceOf('\Exception', $instance);
        $this->assertSame($instance, InstanceManager::get('foo'));
        $this->assertSame('foo', $instance->getMessage());
    }

    public function testForceNewInstance()
    {
        InstanceManager::register('foo', '\Exception', ['foo', 500]);

        $instance = InstanceManager::get('foo');
        $this->assertInstanceOf('\Exception', $instance);

        $newInstance = InstanceManager::get('foo', false);
        $this->assertInstanceOf('\Exception', $instance);
        $this->assertNotSame($instance, $newInstance);
    }

    public function testCallStatic()
    {
        InstanceManager::register('bar', '\Exception', ['foo', 500]);

        $instance = InstanceManager::get('bar');
        $this->assertInstanceOf('\Exception', $instance);
        $this->assertSame($instance, InstanceManager::bar());
    }

    public function testCallStaticForceNewInstace()
    {
        InstanceManager::register('bar', '\Exception', ['foo', 500]);

        $instance = InstanceManager::get('bar');
        $this->assertInstanceOf('\Exception', $instance);
        $this->assertNotSame($instance, InstanceManager::bar(false));
    }

    public function testGetByClassName()
    {
        InstanceManager::register('\Exception', ['foo', 500]);

        $instance = InstanceManager::get('\Exception');
        $this->assertInstanceOf('\Exception', $instance);
        $this->assertSame($instance, InstanceManager::get('\Exception'));
    }

    public function testDirectlyRegister()
    {
        $instance = new \InvalidArgumentException();
        InstanceManager::register($instance);
        $this->assertSame($instance, InstanceManager::get('InvalidArgumentException'));

        $instance = new \InvalidArgumentException();
        InstanceManager::register('baz', $instance);
        $this->assertSame($instance, InstanceManager::get('baz'));
    }

    public function testArgumentAllowClosure()
    {
        InstanceManager::register('qux', '\Exception', function() {
            return ['qux', 501];
        });

        $instance = InstanceManager::get('qux');
        $this->assertInstanceOf('\Exception', $instance);
        $this->assertSame('qux', $instance->getMessage());
        $this->assertSame(501, $instance->getCode());
    }

    public function testCallback()
    {
        InstanceManager::register('foobar', '\Exception', ['foobar'], function($instance) use(&$message) {
            $message = $instance->getMessage();
        });

        InstanceManager::get('foobar');
        $this->assertSame('foobar', $message);
    }

    /**
     * @expectedException Lazy\Util\InstanceManager\Exception\Exception
     * @expectedExceptionMessage Callback must be a \Closure. string given
     */
    public function testInvalidCallbackShouldThrowAnException()
    {
        InstanceManager::register('barbaz', '\Exception', [], 'invalidcallback');
    }
}