<?php

namespace SlothsTest\View;

use SlothsTest\TestCase;
use Sloths\View\View;

/**
 * @covers Sloths\View\View
 */
class ViewTest extends TestCase
{
    public function testDirectory()
    {
        $view = new View();
        $view->setDirectory(__DIR__);
        $this->assertSame(__DIR__, $view->getDirectory());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidDirectoryShouldThrowAnException()
    {
        $view = new View();
        $view->setDirectory('foo');
    }

    public function testVariable()
    {
        $view = new View();
        $view->setVariable('foo', 'foo');
        $view->setVariables(['bar' => 'bar']);

        $this->assertTrue($view->hasVariable('foo'));
        $this->assertSame('foo', $view->getVariable('foo'));
        $this->assertNull($view->getVariable('baz'));
        $this->assertSame(['foo' => 'foo', 'bar' => 'bar'], $view->getVariables());
    }

    public function testRender()
    {
        $view = new View();
        $result = $view->render(__DIR__ . '/fixtures/template', ['foo' => 'foo']);
        $this->assertSame('template foo', $result);

        $view->setDirectory(__DIR__);
        $result = $view->render('fixtures/template', ['foo' => 'foo']);
        $this->assertSame('template foo', $result);

    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRenderUnExistingTemplateShouldThrowAnException()
    {
        $view = new View();
        $view->render('foo');
    }

    public function testRenderWithLayout()
    {
        $view = new View();
        $view->setDirectory(__DIR__ . '/fixtures');
        $view->setLayout('layout');

        $result = $view->render('template', ['foo' => 'foo']);
        $this->assertSame('layout foo template foo', $result);
    }

    public function testRenderWithExceptionThrown()
    {
        $view = new View();

        try {
            $view->render(__DIR__ . '/fixtures/test-exception-thrown');
        } catch (\Exception $e) {

        }

        $this->assertSame('exception thrown', $e->getMessage());
    }

    public function testCustomException()
    {
        $view = new View();
        $view->setExtension('.tpl');

        $result = $view->render(__DIR__ . '/fixtures/custom-extension', ['foo' => 'foo']);
        $this->assertSame('custom extension foo', $result);
    }

    public function testCustomHelper()
    {
        $view = new View();
        $view->setHelpers(['foo' => function() {return 'foo';}]);
        $this->assertSame('foo', $view->foo());
    }

    public function testCallDefaultHelper()
    {
        $view = new View();
        $this->assertSame('&lt;div&gt;', $view->escape('<div>'));
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testCallUndefinedHelperShouldThrowAnException()
    {
        $view = new View();
        $view->foobarbaz();
    }
}