<?php

namespace LazyTest\View;

use Lazy\View\Helper\AbstractHelper;
use Lazy\View\View;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAndGetVars()
    {
        $vars = ['foo' => 'bar', 'bar'=>'baz'];
        $view = new View();
        $view->setVars($vars);
        $this->assertSame($vars, $view->getVars());
    }

    public function testSetAndGetVar()
    {
        $view = new View();
        $view->setVar('foo', 'bar');
        $this->assertSame('bar', $view->getVar('foo'));
        $this->assertNull($view->getVar('bar'));
    }

    public function testSetAndGetPath()
    {
        $view = new View();
        $view->setPath(__DIR__);
        $this->assertSame(__DIR__, $view->getPath());
    }

    public function testSetAndGetExtension()
    {
        $view = new View();
        $view->setExtension('foo');
        $this->assertSame('foo', $view->getExtension());
    }

    public function testConfig()
    {
        $view = new View();
        $view->config([
            'path' => __DIR__,
            'layout' => 'foo',
            'extension' => 'bar'
        ]);

        $this->assertSame(__DIR__, $view->getPath());
        $this->assertSame('foo', $view->getLayout());
        $this->assertSame('bar', $view->getExtension());
    }

    public function testSetAndGetFile()
    {
        $view = new View();
        $view->setFile('foo');
        $this->assertSame('foo', $view->getFile());
    }

    public function testSetAndGetLayout()
    {
        $view = new View();
        $view->setLayout('foo');
        $this->assertSame('foo', $view->getLayout());
    }

    public function testGetFilePath()
    {
        $view = new View();
        $this->assertNull($view->getFilePath());

        $view->setPath(__DIR__);
        $view->setFile('foo');
        $this->assertSame(__DIR__ . '/foo.html.php', $view->getFilePath());
    }

    public function testGetLayoutFilePath()
    {
        $view = new View();
        $this->assertNull($view->getLayoutFilePath());

        $view->setPath(__DIR__);
        $view->setLayout('foo');
        $this->assertSame(__DIR__ . '/_layouts/foo.html.php', $view->getLayoutFilePath());
    }

    public function testRender()
    {
        $view = new View();
        $content = $view->render(__DIR__ . '/fixtures/foo', ['bar' => 'bar']);
        $this->assertSame('foo bar', $content);

        $content = $view->render(['bar' => 'baz']);
        $this->assertSame('foo baz', $content);
    }

    public function testLayout()
    {
        $view = new View();
        $content = $view
            ->setLayout(__DIR__ . '/fixtures/_layouts/layout1')
            ->render(__DIR__ . '/fixtures/foo', ['bar' => 'bar']);
        $this->assertSame('layout1 foo bar', $content);
    }

    public function testNestedLayout()
    {
        $view = new View();
        $content = $view
            ->setPath(__DIR__ . '/fixtures')
            ->setLayout('layout2')
            ->render('foo', ['bar' => 'bar']);
        $this->assertSame('layout1 layout2 foo bar', $content);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRenderWithNoFileShouldThrowAnException()
    {
        $view = new View();
        $view->render();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRenderWithNonExistingFileShouldThrowAnException()
    {
        $view = new View();
        $view->render('foooo');
    }

    public function testEscape()
    {
        $view = new View();
        $this->assertSame('&lt;foo&gt;', $view->escape('<foo>'));
    }

    public function testCustomHelper()
    {
        View::addHelperNamespace('LazyTest\View');
        $view = new View();
        $this->assertSame('foo', $view->customHelper());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidHelperShouldThrowAnException()
    {
        View::addHelperNamespace('LazyTest\View');
        $view = new View();
        $view->invalidHelper();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testCallUndefinedMethodShouldThrowAnException()
    {
        $view = new View();
        $view->fooooo();
    }
}

class CustomHelper extends AbstractHelper
{
    public function customHelper()
    {
        return 'foo';
    }
}

class InvalidHelper extends AbstractHelper
{

}