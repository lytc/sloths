<?php

namespace LazyTest\View;
use Lazy\View\View;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    public function testPath()
    {
        $view = new View();
        $this->assertNull($view->path());
        $view->path('/foo/bar');
        $this->assertSame('/foo/bar', $view->path());
    }

    public function testLayoutMethod()
    {
        $view = new View();
        $this->assertNull($view->layout());
        $view->layout('foo');
        $this->assertSame('/layouts/foo.php', $view->layout());
    }

    public function testTemplateMethod()
    {
        $view = new View();
        $this->assertNull($view->template());
        $view->template('foo');
        $this->assertSame('/foo.php', $view->template());
    }

    public function testVariablesMethod()
    {
        $view = new View();
        $this->assertSame([], $view->variables());
        $view->variables('foo', 'bar');
        $this->assertSame(['foo' => 'bar'], $view->variables());
        $view->variables(['bar' => 'baz']);
        $this->assertSame(['foo' => 'bar', 'bar' => 'baz'], $view->variables());
        $this->assertSame('bar', $view->variables('foo'));
    }

    public function testRender()
    {
        $view = new View();
        $result = $view->render(__DIR__ . '/fixtures/views/test', ['foo' => 'bar']);
        $this->assertSame('barbaz', $result);
    }

    public function testRenderWithLayout()
    {
        $view = new View();
        $view->path(__DIR__ . '/fixtures/views')
            ->layoutPath(__DIR__ . '/fixtures/views')
            ->layout('layout');

        $result = $view->render('test', ['foo' => 'bar']);
        $this->assertSame('layoutbarbaz', $result);
    }

    public function testDisplay()
    {
        $view = new View();
        ob_start();
        $view->display(__DIR__ . '/fixtures/views/test', ['foo' => 'bar']);
        $this->assertSame('barbaz', ob_get_clean());
    }

    /**
     * @expectedException \Lazy\View\Exception\Exception
     * @expectedExceptionMessage View file not found: /foo.php
     */
    public function testWrongTemplateShouldThrowAnException()
    {
        $view = new View();
        $view->render('foo');
    }

    /**
     * @expectedException \Lazy\View\Exception\Exception
     * @expectedExceptionMessage Call undefined method foo
     */
    public function testCallUndefinedMethodShouldThrowAnException()
    {
        $view = new View();
        $view->foo();
    }

    public function testHelper()
    {
        $view = new View();
        $this->assertSame('&lt;foo&gt;', $view->escape('<foo>'));
    }

    public function testConstructorConfig()
    {
        $view = new View([
            'path'  => '/foo',
            'variables' => ['foo' => 'bar']
        ]);

        $this->assertSame('/foo', $view->path());
        $this->assertSame(['foo' => 'bar'], $view->variables());
    }
}