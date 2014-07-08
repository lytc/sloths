<?php

namespace SlothsTest\View\Helper;

use Sloths\View\View;
use Sloths\View\Helper\AssetTag;
use Sloths\View\Helper\StylesheetTag;

/**
 * @covers \Sloths\View\Helper\StyleSheetTag<extended>
 */
class StylesheetTagTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        AssetTag::setDefaultBasePath('/assets');
        StylesheetTag::setDefaultBasePath(AssetTag::getDefaultBasePath() . '/stylesheets');
    }

    public function testWithBasePath()
    {
        $view = new View();
        $this->assertSame('<link href="/assets/stylesheets/foo.css" rel="stylesheet">', (String) $view->stylesheetTag('foo.css'));
        $this->assertSame('<link href="/foo.css" rel="stylesheet">', (String) $view->stylesheetTag('/foo.css'));
        $this->assertSame('<link href="http://test.com/foo.css" rel="stylesheet">', (String) $view->stylesheetTag('http://test.com/foo.css'));
        $this->assertSame('<link href="//test.com/foo.css" rel="stylesheet">', (String) $view->stylesheetTag('//test.com/foo.css'));
        $this->assertSame('<link href="//test.com/foo.css?foo" rel="stylesheet">', (String) $view->stylesheetTag('//test.com/foo.css')->setDisableCachingParam('foo'));
    }

    public function testMultipleSource()
    {
        $view = new View();
        $expected = '<link href="/assets/stylesheets/foo.css" rel="stylesheet"><link href="/assets/stylesheets/bar.css" rel="stylesheet">';
        $this->assertSame($expected, (String) $view->stylesheetTag(['foo.css', 'bar.css']));
    }

    public function testCustomAttribute()
    {
        $view = new View();
        $this->assertSame('<link href="/assets/stylesheets/foo.css" rel="stylesheet" media="screen">', (String) $view->stylesheetTag('foo.css')->setAttribute('media', 'screen'));
    }
}