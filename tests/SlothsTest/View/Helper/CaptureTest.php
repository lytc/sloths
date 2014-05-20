<?php

namespace SlothsTest\View\Helper;
use Sloths\View\Capture;
use Sloths\View\View;

class CaptureTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $capture = new Capture();
        $capture->append('foo')->append('bar')->prepend('baz');
        $this->assertSame('bazfoobar', $capture->__toString());
    }

    public function testSetAndGetRenderer()
    {
        $renderer = function() {};
        $capture = new Capture();
        $capture->setRenderer($renderer);
        $this->assertSame($renderer, $capture->getRenderer());
    }

    public function testRenderWithCallback()
    {
        $capture = new Capture();
        $capture->append('foo')->append('bar')->prepend('baz');
        $content = $capture->render(function($item) {
            return sprintf('<%s>', $item);
        });
        $expected = '<baz><foo><bar>';
        $this->assertSame($expected, $content);
    }

    public function testAssociateWithViewHelper()
    {
        $view = new View();
        $view->capture('js', 'foo.js');
        $view->capture('js', 'bar.js');
        $result = $view->capture('js')->render([$view, 'scriptTag']);
        $expected = '<script src="/foo.js"></script><script src="/bar.js"></script>';
        $this->assertSame($expected, $result);
    }

    public function testReset()
    {
        $capture = new Capture();
        $capture->append('foo')->append('bar')->prepend('baz');
        $this->assertSame('bazfoobar', $capture->__toString());

        $capture->reset();
        $this->assertSame('', $capture->__toString());
    }
}

