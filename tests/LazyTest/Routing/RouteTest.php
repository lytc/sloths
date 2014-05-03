<?php

namespace LazyTest\Route;

use Lazy\Routing\Route;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    public function getMethodMethod()
    {
        $route = new Route('GET', '/');
        $this->assertSame(['GET'], $route->getMethod());

        $route = new Route('GET POST', '/');
        $this->assertSame(['GET', 'POST'], $route->getMethod());
    }

    public function testSetMethod()
    {
        $route = new Route('GET', '/');
        $this->assertSame(['GET' => 'GET'], $route->getMethod());
    }

    public function testGetPattern()
    {
        $route = new Route('GET', '/foo');
        $this->assertSame('/foo', $route->getPattern());
    }

    public function testGetSetCallback()
    {
        $callback = function() {};
        $route = new Route('GET', '/foo', $callback);
        $this->assertSame($callback, $route->getCallback());

        $callback = function() {};
        $route->setCallback($callback);
        $this->assertSame($callback, $route->getCallback());
    }

    public function testBasic()
    {
        $route = new Route('GET', '/');
        $this->assertSame([], $route->match('GET', '/'));

        $route = new Route('GET', '/');
        $this->assertFalse($route->match('POST', '/'));
    }

    public function testMultipleMethod()
    {
        $route = new Route('GET POST', '/');
        $this->assertSame([], $route->match('GET', '/'));
        $this->assertSame([], $route->match('POST', '/'));
        $this->assertFalse($route->match('DELETE', '/'));
    }


    public function testNamedFooShouldNotMatchWithPathFooBar()
    {
        $route = new Route('GET', '/:foo');
        $this->assertFalse($route->match('GET', '/foo/bar'));
    }

    public function testOptionalNamed()
    {
        $route = new Route('GET', '/:foo?/?:bar?');
        $this->assertSame(['foo' => 'hello', 'bar' => 'world'], $route->match('GET', '/hello/world'));
        $this->assertSame(['foo' => 'hello', 'bar' => null], $route->match('GET', '/hello'));

        $route = new Route('GET', '/?:foo?/:bar.html');
        $this->assertSame(['foo' => 'hello', 'bar' => 'world'], $route->match('GET', '/hello/world.html'));
        $this->assertSame(['foo' => '', 'bar' => 'hello'], $route->match('GET', '/hello.html'));
    }

    public function testRegexPattern()
    {
        $route = new Route('GET', '#/hello/(?<foo>[^/?\#]+)');
        $this->assertSame(['foo' => 'world'], $route->match('GET', '/hello/world'));
    }

    public function testRegexNamedPattern()
    {
        $route = new Route('GET', '#/page(?<format>.[^/?\#]+)?');
        $this->assertSame(['format' => '.html'], $route->match('GET', '/page.html'));
        $this->assertSame([], $route->match('GET', '/page'));
    }

    public function testSplatPattern()
    {
        $route = new Route('GET', '/*');
        $this->assertSame(['splat' => ['foo']], $route->match('GET', '/foo'));

        $route = new Route('GET', '/*/foo/*/*');
        $this->assertSame(['splat' => ['bar', 'bling', 'baz/boom']], $route->match('GET', '/bar/foo/bling/baz/boom'));
    }

    public function testNamedAndSplatPattern()
    {
        $route = new Route('GET', '/:foo/*');
        $this->assertSame(['foo' => 'foo', 'splat' => ['bar/baz']], $route->match('GET', '/foo/bar/baz'));
    }

    public function testNamedHasAtChar()
    {
        $route = new Route('GET', '/:foo/:bar');
        $this->assertSame(['foo' => 'user@example.com', 'bar' => 'name'], $route->match('GET', '/user@example.com/name'));
    }

    public function testNamedHasDotChar()
    {
        $route = new Route('GET', '/:file.:ext');
        $this->assertSame(['file' => 'pony', 'ext' => 'jpg'], $route->match('GET', '/pony.jpg'));
    }

    public function testStaticPatternHasDotChar()
    {
        $route = new Route('GET', '/test.bar');
        $this->assertSame([], $route->match('GET', '/test.bar'));
    }

    public function testStaticPatternHasDollarChar()
    {
        $route = new Route('GET', '/test$/');
        $this->assertSame([], $route->match('GET', '/test$/'));
    }

    public function testEncodedPlusChar()
    {
        $route = new Route('GET', '/te+st/');
        $this->assertSame([], $route->match('GET', '/te%2Bst/'));
    }

    public function testPlusChar()
    {
        $route = new Route('GET', '/te+st/');
        $this->assertFalse($route->match('GET', '/teeeeeeest/'));
    }

    public function testNamedAllowPlusChar()
    {
        $route = new Route('GET', '/:test');
        $this->assertSame(['test' => 'bob ross'], $route->match('GET', '/bob+ross'));
    }

    public function testParenChars()
    {
        $route = new Route('GET', '/test(bar)/');
        $this->assertSame([], $route->match('GET', '/test(bar)/'));
    }

    public function testWithWhiteSpacesEncoded()
    {
        $route = new Route('GET', '/path with spaces');
        $this->assertSame([], $route->match('GET', '/path%20with%20spaces'));

        $route = new Route('GET', '/path with spaces');
        $this->assertSame([], $route->match('GET', '/path%20with%20spaces'));
    }

    public function testNamedAllowAmperChar()
    {
        $route = new Route('GET', '/:name');
        $this->assertSame(['name' => 'foo&bar'], $route->match('GET', '/foo&bar'));
    }

    public function testNamedAndSplatAllowWhiteSpaces()
    {
        $route = new Route('GET', '/:foo/*');
        $this->assertSame(['foo' => 'hello world', 'splat' => ['how are you']], $route->match('GET', '/hello%20world/how%20are%20you'));
    }

    public function testRegexWithDots()
    {
        $route = new Route('GET', '#/foo.../bar');
        $this->assertSame([], $route->match('GET', '/foooom/bar'));
    }

    public function testRegex()
    {
        $route = new Route('GET', '#/fo(.*)/ba(.*)');
        $this->assertSame(['orooomma', 'f'], $route->match('GET', '/foorooomma/baf'));
    }

    public function testNamedWithDoubleColonShouldJustMatchInteger()
    {
        $route = new Route('GET', '/::foo');
        $this->assertFalse($route->match('GET', '/foo'));
        $this->assertFalse($route->match('GET', '/:foo'));
        $this->assertFalse($route->match('GET', '/123a'));
        $this->assertFalse($route->match('GET', '/a123'));
        $this->assertFalse($route->match('GET', '/a123b'));
        $this->assertSame(['foo' => 123], $route->match('GET', '/123'));

        $route = new Route('GET', '/::id/bar');
        $this->assertFalse($route->match('GET', '/foo/bar'));
        $this->assertFalse($route->match('GET', '/:foo/'));
        $this->assertFalse($route->match('GET', '/123a/bar'));
        $this->assertFalse($route->match('GET', '/a123/foo'));
        $this->assertFalse($route->match('GET', '/a123b/bar'));
        $this->assertFalse($route->match('GET', '/123'));
        $this->assertSame(['id' => 123], $route->match('GET', '/123/bar'));
    }
}