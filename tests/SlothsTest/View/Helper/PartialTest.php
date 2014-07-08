<?php

namespace SlothsTest\View\Helper;

use Sloths\View\View;

/**
 * @covers \Sloths\View\Helper\Partial
 */
class PartialTest extends \PHPUnit_Framework_TestCase
{
    protected $viewPath;

    protected function setUp()
    {
        $this->viewPath = __DIR__ . '/../fixtures/partial-test';
    }

    public function test()
    {
        $view = new View();
        $view->setDirectory($this->viewPath);
        $view->setVar('foo', 'bar');
        $expected = 'foo partial bar';

        $this->assertSame($expected, $view->render('template'));
    }

    public function testOverrideVar()
    {
        $view = new View();
        $view->setDirectory($this->viewPath);
        $view->setVar('foo', 'bar');
        $expected = 'foo partial barbaz';

        $this->assertSame($expected, $view->render('template2'));
    }
}