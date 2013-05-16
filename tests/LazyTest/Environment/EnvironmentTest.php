<?php

namespace LazyTest\Environment;

use Lazy\Environment\Environment;

class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
    public function testServerVarsMethod()
    {
        $env = new Environment();
        $this->assertSame($_SERVER, $env->serverVars());
    }

    public function testParamsGet()
    {
        $env = new Environment();
        $this->assertSame($_GET, $env->paramsGet());
    }

    public function testParamsPost()
    {
        $env = new Environment();
        $this->assertSame($_POST, $env->paramsPost());
    }

    public function testParamsCookie()
    {
        $env = new Environment();
        $this->assertSame($_COOKIE, $env->paramsCookie());
    }

    public function testParamsFile()
    {
        $env = new Environment();
        $this->assertSame($_FILES, $env->paramsFile());
    }

    public function testAliasMethods()
    {
        $env = new Environment();
        $this->assertSame($env->serverVars(), $env->serverVar());
        $this->assertSame($env->paramsGet(), $env->paramGet());
        $this->assertSame($env->paramsPost(), $env->paramPost());
        $this->assertSame($env->paramsCookie(), $env->paramCookie());
        $this->assertSame($env->paramsFile(), $env->paramFile());
    }

    public function testGetServerVar()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $env = new Environment();
        $this->assertSame('GET', $env->serverVar('REQUEST_METHOD'));
    }

    public function testGetParamGet()
    {
        $_GET['foo'] = 'bar';
        $env = new Environment();
        $this->assertSame('bar', $env->paramGet('foo'));
    }

    public function testGetParamPost()
    {
        $_POST['foo'] = 'bar';
        $env = new Environment();
        $this->assertSame('bar', $env->paramPost('foo'));
    }

    public function testGetParamCookie()
    {
        $_COOKIE['foo'] = 'bar';
        $env = new Environment();
        $this->assertSame('bar', $env->paramCookie('foo'));
    }

    public function testGetParamFile()
    {
        $_FILES['foo'] = 'bar';
        $env = new Environment();
        $this->assertSame('bar', $env->paramFile('foo'));
    }

    public function testSetServerVar()
    {
        $env = new Environment();
        $env->serverVar('REQUEST_METHOD', 'POST');
        $this->assertSame('POST', $env->serverVar('REQUEST_METHOD'));
    }

    public function testParamsMethod()
    {
        $_REQUEST['foo'] = 'bar';
        $_GET['bar'] = 'baz';
        $env = new Environment();
        $this->assertSame(['foo' => 'bar'], $env->params());

        $this->assertSame('baz', $env->param('bar'));

        $this->assertNull($env->param('non_existing_param'));
    }

    public function testSetParamGet()
    {
        $_GET = [];
        $env = new Environment;
        $env->paramGet(['foo' => 'bar']);
        $this->assertSame('bar', $env->paramGet('foo'));
    }

    public function testSetParamGetAndResetCurrentParams()
    {
        $_GET = [];
        $env = new Environment;
        $env->paramGet('foo', 'bar');
        $this->assertSame('bar', $env->paramGet('foo'));

        $env->paramGet(true, ['bar' => 'baz']);
        $this->assertNull($env->paramGet('foo'));
        $this->assertSame('baz', $env->paramGet('bar'));
    }

    /**
     * @expectedException \Lazy\Environment\Exception\Exception
     * @expectedExceptionMessage Call undefined method foo
     */
    public function testCallUndefinedMethodShouldThrowAnException()
    {
        $env = new Environment();
        $env->foo();
    }
}