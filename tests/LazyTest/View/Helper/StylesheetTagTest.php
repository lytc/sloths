<?php

namespace LazyTest\View\Helper;

use Lazy\View\View;
use Lazy\View\Helper\AssetTag;
use Lazy\View\Helper\StylesheetTag;

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
        $this->assertSame('<link href="/assets/stylesheets/foo.css?__dc" rel="stylesheet">', (String) $view->stylesheetTag('foo.css'));
        $this->assertSame('<link href="/foo.css?__dc" rel="stylesheet">', (String) $view->stylesheetTag('/foo.css'));
        $this->assertSame('<link href="http://test.com/foo.css?__dc" rel="stylesheet">', (String) $view->stylesheetTag('http://test.com/foo.css'));
        $this->assertSame('<link href="//test.com/foo.css?__dc" rel="stylesheet">', (String) $view->stylesheetTag('//test.com/foo.css'));
        $this->assertSame('<link href="//test.com/foo.css?foo" rel="stylesheet">', (String) $view->stylesheetTag('//test.com/foo.css')->setDisableCachingParam('foo'));
    }

    public function testMultipleSource()
    {
        $view = new View();
        $expected = '<link href="/assets/stylesheets/foo.css?__dc" rel="stylesheet"><link href="/assets/stylesheets/bar.css?__dc" rel="stylesheet">';
        $this->assertSame($expected, (String) $view->stylesheetTag(['foo.css', 'bar.css']));
    }

    public function testCustomAttribute()
    {
        $view = new View();
        $this->assertSame('<link href="/assets/stylesheets/foo.css?__dc" rel="stylesheet" media="screen">', (String) $view->stylesheetTag('foo.css')->setAttribute('media', 'screen'));
    }
}