<?php

namespace SlothsTest\Http\Message;

use Sloths\Http\Message\AbstractRequest;
use Sloths\Http\Message\Parameters;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Http\Message\AbstractRequest
 */
class AbstractRequestTest extends TestCase
{
    public function testMethod()
    {
        $request = new Request();
        $request->setMethod(AbstractRequest::METHOD_POST);
        $this->assertSame(AbstractRequest::METHOD_POST, $request->getMethod());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetUnSupportedMethodShouldThrowAnException()
    {
        $request = new Request();
        $request->setMethod('foo');
    }

    public function testUrl()
    {
        $request = new Request();
        $request->setUrl('http://example.com');
        $this->assertSame('http://example.com', $request->getUrl());

        $request->setQueryParams(['foo' => 'bar']);
        $this->assertSame('http://example.com?foo=bar', $request->getUrl());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidUrlShouldThrowAnException()
    {
        $request = new Request();
        $request->setUrl('foo');
    }

    public function testQueryParams()
    {
        $request = new Request();
        $this->assertInstanceOf('Sloths\Http\Message\Parameters', $request->getQueryParams());

        $params = new Parameters();
        $request->setQueryParams($params);
        $this->assertSame($params, $request->getQueryParams());

        $request->setQueryParams(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $request->getQueryParams()->toArray());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidQueryParamsShouldThrowAnException()
    {
        $request = new Request();
        $request->setQueryParams('foo');
    }

    public function testPostParams()
    {
        $request = new Request();
        $this->assertInstanceOf('Sloths\Http\Message\Parameters', $request->getPostParams());

        $params = new Parameters();
        $request->setPostParams($params);
        $this->assertSame($params, $request->getPostParams());

        $request->setPostParams(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $request->getPostParams()->toArray());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidPostParamsShouldThrowAnException()
    {
        $request = new Request();
        $request->setPostParams('foo');
    }

    public function testParams()
    {
        $request = new Request();
        $this->assertInstanceOf('Sloths\Http\Message\Parameters', $request->getParams());

        $params = new Parameters();
        $request->setParams($params);
        $this->assertSame($params, $request->getParams());

        $request->setParams(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $request->getParams()->toArray());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidParamsShouldThrowAnException()
    {
        $request = new Request();
        $request->setParams('foo');
    }

    public function testFileParams()
    {
        $request = new Request();
        $this->assertInstanceOf('Sloths\Http\Message\Parameters', $request->getFileParams());

        $files = new Parameters();
        $request->setFileParams($files);
        $this->assertSame($files, $request->getFileParams());

        $request->setFileParams(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $request->getFileParams()->toArray());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidFileParamsShouldThrowAnException()
    {
        $request = new Request();
        $request->setFileParams('foo');
    }

    public function testCookieParams()
    {
        $request = new Request();
        $this->assertInstanceOf('Sloths\Http\Message\Parameters', $request->getCookieParams());

        $cookies = new Parameters();
        $request->setCookieParams($cookies);
        $this->assertSame($cookies, $request->getCookieParams());

        $request->setCookieParams(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $request->getCookieParams()->toArray());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidCookieParamsShouldThrowAnException()
    {
        $request = new Request();
        $request->setCookieParams('foo');
    }

    public function test__get()
    {
        $request = new Request();
        $this->assertSame($request->queryParams, $request->getQueryParams());
        $this->assertSame($request->postParams, $request->getPostParams());
        $this->assertSame($request->params, $request->getParams());
        $this->assertSame($request->files, $request->getFileParams());
        $this->assertSame($request->cookies, $request->getCookieParams());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function test__getShouldUndefinedPropertyThrowAnException()
    {
        $request = new Request();
        $request->foobar;
    }

    public function test__set()
    {
        $request = new Request();
        $params = ['foo' => 'bar'];

        $request->queryParams = $params;
        $this->assertSame($params, $request->getQueryParams()->toArray());

        $request->postParams = $params;
        $this->assertSame($params, $request->getPostParams()->toArray());

        $request->params = $params;
        $this->assertSame($params, $request->getParams()->toArray());

        $request->files = $params;
        $this->assertSame($params, $request->getFileParams()->toArray());

        $request->cookies = $params;
        $this->assertSame($params, $request->getCookieParams()->toArray());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function test__setShouldUndefinedPropertyThrowAnException()
    {
        $request = new Request();
        $request->foobar = 'foo';
    }
}

class Request extends AbstractRequest
{}