<?php

namespace LazyTest\View\Helper;

use Lazy\View\View;

class PartialLoopTest extends \PHPUnit_Framework_TestCase
{
    protected $viewPath;

    protected function setUp()
    {
        $this->viewPath = __DIR__ . '/../fixtures/partial-loop-test';
    }

    public function testWithPureArray()
    {
        $view = new View();
        $view->setPath($this->viewPath);
        $view->setVar('items', [['name' => 'foo'], ['name' => 'bar']]);

        $expected = 'foo foobar';

        $this->assertSame($expected, $view->render('template'));
    }
}