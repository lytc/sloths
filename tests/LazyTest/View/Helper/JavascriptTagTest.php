<?php

namespace LazyTest\View;
use Lazy\View\View;

class JavascriptTagTest extends \PHPUnit_Framework_TestCase
{
    public function testJavascriptTag()
    {
        $view = new View();
        $this->assertSame('<script src="foo.js"></script>', $view->javascriptTag('foo.js'));
        $this->assertSame('<script src="foo.js"></script>', $view->javascriptTag('foo'));

        $time = '12345';
        $view = new View([
            'helpers' => [
                'javascriptTag' => [
                    'assetStamp' => $time
                ]
            ]
        ]);

        $this->assertSame('<script src="foo.js?' . $time . '"></script>', $view->javascriptTag('foo'));

        $view = new View([
            'helpers' => [
                'javascriptTag' => [
                    'assetStamp' => true
                ]
            ]
        ]);

        $this->assertSame('<script src="foo.js?' . time() . '"></script>', $view->javascriptTag('foo'));
    }
}