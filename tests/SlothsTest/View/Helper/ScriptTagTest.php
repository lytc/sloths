<?php

namespace SlothsTest\View\Helper;

use Sloths\View\View;
use Sloths\View\Helper\AssetTag;
use Sloths\View\Helper\ScriptTag;

/**
 * @covers \Sloths\View\Helper\ScriptTag<extended>
 */
class ScriptTagTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        AssetTag::setDefaultBasePath('/assets');
        ScriptTag::setDefaultBasePath(AssetTag::getDefaultBasePath() . '/javascripts');
    }

    public function testWithBasePath()
    {
        $view = new View();
        $this->assertSame('<script src="/assets/javascripts/foo.js"></script>', (String) $view->scriptTag('foo.js'));
        $this->assertSame('<script src="/foo.js"></script>', (String) $view->scriptTag('/foo.js'));
        $this->assertSame('<script src="http://test.com/foo.js"></script>', (String) $view->scriptTag('http://test.com/foo.js'));
        $this->assertSame('<script src="//test.com/foo.js"></script>', (String) $view->scriptTag('//test.com/foo.js'));
        $this->assertSame('<script src="//test.com/foo.js?foo"></script>', (String) $view->scriptTag('//test.com/foo.js')->setDisableCachingParam('foo'));
    }

    public function testMultipleSource()
    {
        $view = new View();
        $expected = '<script src="/assets/javascripts/foo.js"></script><script src="/assets/javascripts/bar.js"></script>';
        $this->assertSame($expected, (String) $view->scriptTag(['foo.js', 'bar.js']));
        $this->assertSame($expected, (String) $view->scriptTag('foo.js', 'bar.js'));
    }

    public function testAppendAndPrepend()
    {
        $view = new View();
        $expected = '<script src="/assets/javascripts/baz.js"></script><script src="/assets/javascripts/foo.js"></script><script src="/assets/javascripts/bar.js"></script>';
        $scriptTag = $view->scriptTag('foo.js')->append('bar.js')->prepend('baz.js');
        $this->assertSame($expected, (String) $scriptTag);
    }
}