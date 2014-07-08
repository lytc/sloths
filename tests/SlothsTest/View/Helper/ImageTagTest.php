<?php

namespace SlothsTest\View\Helper;

use Sloths\View\View;
use Sloths\View\Helper\AssetTag;
use Sloths\View\Helper\ImageTag;

/**
 * @covers \Sloths\View\Helper\ImageTag<extended>
 */
class ImageTagTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        AssetTag::setDefaultBasePath('/assets');
        ImageTag::setDefaultBasePath(AssetTag::getDefaultBasePath() . '/images');
    }

    public function testWithBasePath()
    {
        $view = new View();
        $this->assertSame('<img src="/assets/images/foo.jpg">', (String) $view->imageTag('foo.jpg'));
        $this->assertSame('<img src="/foo.jpg">', (String) $view->imageTag('/foo.jpg'));
        $this->assertSame('<img src="http://test.com/foo.jpg">', (String) $view->imageTag('http://test.com/foo.jpg'));
        $this->assertSame('<img src="//test.com/foo.jpg">', (String) $view->imageTag('//test.com/foo.jpg'));
        $this->assertSame('<img src="//test.com/foo.jpg?foo">', (String) $view->imageTag('//test.com/foo.jpg')->setDisableCachingParam('foo'));
    }

    public function testMultipleSource()
    {
        $view = new View();
        $expected = '<img src="/assets/images/foo.jpg"><img src="/assets/images/bar.jpg">';
        $this->assertSame($expected, (String) $view->imageTag(['foo.jpg', 'bar.jpg']));
        $this->assertSame($expected, (String) $view->imageTag('foo.jpg', 'bar.jpg'));
    }

    public function testAppendAndPrepend()
    {
        $view = new View();
        $expected = '<img src="/assets/images/baz.jpg"><img src="/assets/images/foo.jpg"><img src="/assets/images/bar.jpg">';
        $imageTag = $view->imageTag('foo.jpg')->append('bar.jpg')->prepend('baz.jpg');
        $this->assertSame($expected, (String) $imageTag);
    }
}