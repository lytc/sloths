<?php

namespace LazyTest\View;
use Lazy\View\View;

class CaptureTest extends \PHPUnit_Framework_TestCase
{
    public function testCapture()
    {
        $view = new View();
        $view->capture('foo', 'foobar');
        $this->assertSame('foobar', $view->capture('foo')->__toString());

        $view->capture('bar', ['bar', 'baz']);
        $this->assertSame('barbaz', $view->capture('bar')->__toString());

        $view->capture('baz', function() {
            echo 'bazqux';
        });
        $this->assertSame('bazqux', $view->capture('baz')->__toString());
    }
}