<?php

namespace SlothsTest\View\Helper;

use Sloths\View\Helper\Assets;
use SlothsTest\TestCase;

/**
 * @covers Sloths\View\Helper\Assets
 */
class AssetsTest extends TestCase
{
    public function testIsExternalSource()
    {
        $assets = new Assets();
        $this->assertTrue($assets->isExternalSource('http://example.com/foo.js'));
        $this->assertTrue($assets->isExternalSource('//example.com/foo.js'));
        $this->assertFalse($assets->isExternalSource('/foo.js'));
    }

    public function testApplyVersion()
    {
        $assets = new Assets();
        $assets->setVersion('v1');

        $this->assertSame('/foo.js?___=v1', $assets->applyVersion('/foo.js'));
        $this->assertSame('//example.com/foo.js', $assets->applyVersion('//example.com/foo.js'));

        $assets->setVersionParamName('v');
        $this->assertSame('/foo.js?v=v1', $assets->applyVersion('/foo.js'));
    }

    public function testPrepareSource()
    {
        $assets = new Assets();
        $assets->setBaseUrl('/assets');

        $this->assertSame('/assets/javascripts/foo.js', $assets->prepareSource('javascripts/foo.js'));
        $this->assertSame('//example.com/javascripts/foo.js', $assets->prepareSource('//example.com/javascripts/foo.js'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetNonExistingGroupShouldThrowAnException()
    {
        $assets = new Assets();
        $assets->getGroup('foo');
    }

    public function testGetGroup()
    {
        $assets = new Assets();
        $assets->fromArray([
            'foo' => 'bar.js'
        ]);

        $group = $assets->getGroup('foo');
        $this->assertSame(['bar.js' => 'bar.js'], $group->getJs());
    }

    public function testRender()
    {
        $asserts = new Assets();
        $asserts->fromArray([
            'foo' => 'foo.js',
            'bar' => ['bar.js'],
            'baz' => ['extends' => 'bar'],
            'qux' => [
                'extends' => ['foo', 'baz'],
                'sources' => ['qux.js', 'qux.css']
            ]
        ]);
        $asserts->uses('qux');

        $expected = '<link href="qux.css" rel="stylesheet" />' . PHP_EOL
            . '<script src="foo.js"></script>' . PHP_EOL
            . '<script src="bar.js"></script>' . PHP_EOL
            . '<script src="qux.js"></script>';
        $result = $asserts->render();

        $this->assertSame($expected, $result);
    }
}