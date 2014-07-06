<?php

namespace SlothsTest\Validation\Message;

use Sloths\Http\Message\Parameters;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Http\Message\Parameters
 */
class ParametersTest extends TestCase
{
    public function test()
    {
        $parameters = new Parameters(['foo' => 'bar']);

        $this->assertCount(1, $parameters);
        $this->assertTrue($parameters->has('foo'));
        $this->assertSame('bar', $parameters->get('foo'));

        $parameters->set('bar', 'baz');
        $this->assertCount(2, $parameters);
        $this->assertTrue($parameters->has('bar'));
        $this->assertSame('baz', $parameters->get('bar'));

        $parameters->remove('foo');
        $this->assertCount(1, $parameters);
        $this->assertFalse($parameters->has('foo'));
        $this->assertNull($parameters->get('foo'));
    }

    public function testMagicMethods()
    {
        $parameters = new Parameters();

        $parameters->foo = 'bar';
        $this->assertTrue(isset($parameters->foo));
        $this->assertSame('bar', $parameters->foo);

        unset($parameters->foo);
        $this->assertFalse(isset($parameters->foo));
        $this->assertNull($parameters->foo);
    }

    public function testArrayAccess()
    {
        $parameters = new Parameters();

        $parameters['foo'] = 'bar';
        $this->assertTrue(isset($parameters['foo']));
        $this->assertSame('bar', $parameters['foo']);

        unset($parameters['foo']);
        $this->assertFalse(isset($parameters['foo']));
        $this->assertNull($parameters['foo']);
    }

    public function testToArray()
    {
        $parameters = new Parameters(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $parameters->toArray());
    }

    public function testJsonEncode()
    {
        $params = ['foo' => 'bar'];
        $parameters = new Parameters($params);
        $this->assertSame(json_encode($params), json_encode($parameters));
    }

    public function testTraversable()
    {
        $parameters = new Parameters(['foo' => 'bar', 'bar' => 'baz']);
        $count = 0;
        foreach ($parameters as $name => $value) {
            $count++;
        }
        $this->assertSame(2, $count);
    }
}