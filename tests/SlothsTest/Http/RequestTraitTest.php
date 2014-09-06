<?php

namespace SlothsTest\Http;

use Sloths\Http\RequestInterface;
use Sloths\Http\RequestTrait;
use Sloths\Misc\Parameters;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Http\RequestTrait
 */
class RequestTraitTest extends TestCase
{
    public function test()
    {
        $request = new StubRequest();

        $this->assertInstanceOf('Sloths\Misc\Parameters', $request->getParams());
        $this->assertInstanceOf('Sloths\Misc\Parameters', $request->getParamsQuery());
        $this->assertInstanceOf('Sloths\Misc\Parameters', $request->getParamsPost());
        $this->assertInstanceOf('Sloths\Misc\Parameters', $request->getParamsFile());

        $request
            ->setMethod('POST')
            ->setPath('/foo')
            ->setUrl('http://example.com/foo')
            ->setScheme('https')
            ->setHost('example.com')
            ->setPort(8080)
            ->setParams(['foo' => 'bar'])
            ->setParamsQuery(['foo' => 'bar'])
            ->setParamsPost(['foo' => 'bar'])
            ->setParamsFile(['foo' => 'bar'])

        ;

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('/foo', $request->getPath());
        $this->assertSame('http://example.com/foo?foo=bar', $request->getUrl());
        $this->assertSame('https', $request->getScheme());
        $this->assertSame('example.com', $request->getHost());
        $this->assertSame(8080, $request->getPort());
        $this->assertSame(['foo' => 'bar'], $request->getParams()->toArray());
        $this->assertSame(['foo' => 'bar'], $request->getParamsQuery()->toArray());
        $this->assertSame(['foo' => 'bar'], $request->getParamsPost()->toArray());
        $this->assertSame(['foo' => 'bar'], $request->getParamsFile()->toArray());
    }

    public function testGetParams()
    {
        $request = new StubRequest();
        $request->setParamsQuery(new Parameters(['foo' => 'bar']));
        $request->setParamsPost(new Parameters(['bar' => 'baz']));

        $this->assertSame(['foo' => 'bar', 'bar' => 'baz'], $request->getParams()->toArray());

        $params = new Parameters();
        $request->setParams($params);

        $this->assertSame($params, $request->getParams());
    }

    public function testAccepts()
    {
        $headers = $this->getMock('Sloths\Http\Headers', ['get']);
        $headers->expects($this->atLeast(1))->method('get')->with('ACCEPT')->willReturn('foo,bar');

        $request = new StubRequest();
        $request->setHeaders($headers);

        $this->assertSame(['foo', 'bar'], $request->getAccepts());
        $this->assertTrue($request->isAccept('foo'));
    }

    public function testGetContentType()
    {

        $headers = $this->getMock('Sloths\Http\Headers', ['get']);
        $headers->expects($this->atLeast(1))->method('get')->with('CONTENT_TYPE')->willReturn('foo');

        $request = new StubRequest();
        $request->setHeaders($headers);

        $this->assertSame('foo', $request->getContentType());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidParamsShouldThrowAnException()
    {
        $request = new StubRequest();
        $request->setParams(1);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidParamsQueryShouldThrowAnException()
    {
        $request = new StubRequest();
        $request->setParamsQuery(1);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidParamsPostShouldThrowAnException()
    {
        $request = new StubRequest();
        $request->setParamsPost(1);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidParamsFileShouldThrowAnException()
    {
        $request = new StubRequest();
        $request->setParamsFile(1);
    }
}

class StubRequest implements RequestInterface
{
    use RequestTrait;
}