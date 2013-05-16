<?php

namespace LazyTest\Application;

use Lazy\Application\Route;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $route = new Route('GET', '/');
        $this->assertSame([], $route->matches('GET', '/'));

        $route = new Route('GET', '/');
        $this->assertFalse($route->matches('POST', '/'));
    }

    public function testMultipleMethod()
    {
        $route = new Route('GET POST', '/');
        $this->assertSame([], $route->matches('GET', '/'));
        $this->assertSame([], $route->matches('POST', '/'));
        $this->assertFalse($route->matches('DELETE', '/'));
    }

    public function testNamedAndCondition()
    {
        $route = new Route('GET', '/:name');
        $route->conditions(['name' => '^[a-z]+$']);
        $this->assertSame(['name' => 'foo'], $route->matches('GET', '/foo'));
        $this->assertFalse($route->matches('GET', '/foo123'));
    }

    public function testConditionWithNonExistingParamShouldNotMatch()
    {
        $route = new Route('GET', '/:name');
        $this->assertSame(['name' => 'foo'], $route->matches('GET', '/foo'));
        $route->conditions(['foo' => 'bar']);
        $this->assertFalse($route->matches('GET', '/bar'));
    }

    public function testConditionWithClosure()
    {
        $route = new Route('GET', '/:name');
        $route->conditions(function($params) {
            return $params['name'] == 'foo';
        });
        $this->assertSame(['name' => 'foo'], $route->matches('GET', '/foo'));
        $this->assertFalse($route->matches('GET', '/bar'));
    }

    public function testNamedFooShouldNotMatchWithPathFooBar()
    {
        $route = new Route('GET', '/:foo');
        $this->assertFalse($route->matches('GET', '/foo/bar'));
    }

    public function testOptionalNamed()
    {
        $route = new Route('GET', '/:foo?/?:bar?');
        $this->assertSame(['foo' => 'hello', 'bar' => 'world'], $route->matches('GET', '/hello/world'));
        $this->assertSame(['foo' => 'hello', 'bar' => ''], $route->matches('GET', '/hello'));
    }

    public function testRegexPattern()
    {
        $route = new Route('GET', '#/hello/(?<foo>[^/?\#]+)');
        $this->assertSame(['foo' => 'world'], $route->matches('GET', '/hello/world'));
    }

    public function testRegexNamedPattern()
    {
        $route = new Route('GET', '#/page(?<format>.[^/?\#]+)?');
        $this->assertSame(['format' => '.html'], $route->matches('GET', '/page.html'));
        $this->assertSame([], $route->matches('GET', '/page'));
    }

    public function testSplatPattern()
    {
        $route = new Route('GET', '/*');
        $this->assertSame(['splat' => ['foo']], $route->matches('GET', '/foo'));

        $route = new Route('GET', '/*/foo/*/*');
        $this->assertSame(['splat' => ['bar', 'bling', 'baz/boom']], $route->matches('GET', '/bar/foo/bling/baz/boom'));
    }

    public function testNamedAndSplatPattern()
    {
        $route = new Route('GET', '/:foo/*');
        $this->assertSame(['foo' => 'foo', 'splat' => ['bar/baz']], $route->matches('GET', '/foo/bar/baz'));
    }

    public function testNamedHasAtChar()
    {
        $route = new Route('GET', '/:foo/:bar');
        $this->assertSame(['foo' => 'user@example.com', 'bar' => 'name'], $route->matches('GET', '/user@example.com/name'));
    }

    public function testNamedHasDotChar()
    {
        $route = new Route('GET', '/:file.:ext');
        $this->assertSame(['file' => 'pony', 'ext' => 'jpg'], $route->matches('GET', '/pony.jpg'));
    }

    public function testStaticPatternHasDotChar()
    {
        $route = new Route('GET', '/test.bar');
        $this->assertSame([], $route->matches('GET', '/test.bar'));
    }

    public function testStaticPatternHasDollarChar()
    {
        $route = new Route('GET', '/test$/');
        $this->assertSame([], $route->matches('GET', '/test$/'));
    }

    public function testEncodedPlusChar()
    {
        $route = new Route('GET', '/te+st/');
        $this->assertSame([], $route->matches('GET', '/te%2Bst/'));
    }

    public function testPlusChar()
    {
        $route = new Route('GET', '/te+st/');
        $this->assertFalse($route->matches('GET', '/teeeeeeest/'));
    }

    public function testNamedAllowPlusChar()
    {
        $route = new Route('GET', '/:test');
        $this->assertSame(['test' => 'bob ross'], $route->matches('GET', '/bob+ross'));
    }

    public function testParenChars()
    {
        $route = new Route('GET', '/test(bar)/');
        $this->assertSame([], $route->matches('GET', '/test(bar)/'));
    }

    public function testWithWhiteSpacesEncoded()
    {
        $route = new Route('GET', '/path with spaces');
        $this->assertSame([], $route->matches('GET', '/path%20with%20spaces'));

        $route = new Route('GET', '/path with spaces');
        $this->assertSame([], $route->matches('GET', '/path%20with%20spaces'));
    }

    public function testNamedAllowAmperChar()
    {
        $route = new Route('GET', '/:name');
        $this->assertSame(['name' => 'foo&bar'], $route->matches('GET', '/foo&bar'));
    }

    public function testNamedAndSplatAllowWhiteSpaces()
    {
        $route = new Route('GET', '/:foo/*');
        $this->assertSame(['foo' => 'hello world', 'splat' => ['how are you']], $route->matches('GET', '/hello%20world/how%20are%20you'));
    }

    public function testRegexWithDots()
    {
        $route = new Route('GET', '#/foo.../bar');
        $this->assertSame([], $route->matches('GET', '/foooom/bar'));
    }

    public function testRegex()
    {
        $route = new Route('GET', '#/fo(.*)/ba(.*)');
        $this->assertSame(['orooomma', 'f'], $route->matches('GET', '/foorooomma/baf'));
    }

    public function testNamedWithDoubleColonShouldJustMatchInteger()
    {
        $route = new Route('GET', '/::foo');
        $this->assertFalse($route->matches('GET', '/foo'));
        $this->assertFalse($route->matches('GET', '/:foo'));
        $this->assertFalse($route->matches('GET', '/123a'));
        $this->assertFalse($route->matches('GET', '/a123'));
        $this->assertFalse($route->matches('GET', '/a123b'));
        $this->assertSame(['foo' => 123], $route->matches('GET', '/123'));

        $route = new Route('GET', '/::id/bar');
        $this->assertFalse($route->matches('GET', '/foo/bar'));
        $this->assertFalse($route->matches('GET', '/:foo/'));
        $this->assertFalse($route->matches('GET', '/123a/bar'));
        $this->assertFalse($route->matches('GET', '/a123/foo'));
        $this->assertFalse($route->matches('GET', '/a123b/bar'));
        $this->assertFalse($route->matches('GET', '/123'));
        $this->assertSame(['id' => 123], $route->matches('GET', '/123/bar'));
    }

    public function getMethodMethodAndMethods()
    {
        $route = new Route('GET', '/');
        $this->assertSame(['GET'], $route->methods());
        $this->assertSame(['GET'], $route->method());

        $route = new Route('GET POST', '/');
        $this->assertSame(['GET', 'POST'], $route->methods());
        $this->assertSame(['GET', 'POST'], $route->method());
    }

    public function testSetMethodAndResetTheCurrentMethods()
    {
        $route = new Route('GET', '/');
        $this->assertSame(['GET'], $route->methods());

        $route->methods('POST');
        $this->assertSame(['GET', 'POST'], $route->methods());
        $route->methods(true, 'POST');
        $this->assertSame(['POST'], $route->methods());
    }

    public function testHasMethod()
    {
        $route = new Route('GET POST DELETE', '/');
        $this->assertTrue($route->hasMethods('GET DELETE'));
        $this->assertTrue($route->hasMethods('TRACE GET'));
        $this->assertFalse($route->hasMethods('TRACE'));
    }

    public function testGetPattern()
    {
        $route = new Route('GET', '/foo');
        $this->assertSame('/foo', $route->pattern());
    }

    public function testGetConditions()
    {
        $route = new Route('GET', '/');
        $this->assertSame([], $route->conditions());
        $route->conditions(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $route->conditions());
    }

    public function testSetConditionAndResetTheCurrentValue()
    {
        $route = new Route('GET', '/');
        $this->assertSame([], $route->conditions());

        $route->conditions(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $route->conditions());

        $route->conditions(['bar' => 'baz']);
        $this->assertSame(['foo' => 'bar', 'bar' => 'baz'], $route->conditions());

        $route->conditions(true,['baz' => 'qux']);
        $this->assertSame(['baz' => 'qux'], $route->conditions());
    }
}