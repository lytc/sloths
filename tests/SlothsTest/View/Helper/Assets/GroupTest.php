<?php

namespace SlothsTest\View\Helper\Assets;

use Sloths\View\Helper\Assets\Group;
use SlothsTest\TestCase;

class GroupTest extends TestCase
{
    public function testExtend()
    {
        $parentGroup = $this->getMock('ParentGroup', ['getCss', 'getJs']);
        $parentGroup->expects($this->once())->method('getCss')->willReturn(['foo.css' => 'foo.css', 'bar.css' => 'bar.css']);
        $parentGroup->expects($this->once())->method('getJs')->willReturn(['foo.js' => 'foo.js', 'bar.js' => 'bar.js']);

        $assets = $this->getMock('Sloths\View\Helper\Assets', ['getGroup']);
        $assets->expects($this->any())->method('getGroup')->with('parent')->willReturn($parentGroup);

        $group = new Group($assets);
        $group->extend('parent');
        $group->addCss('baz.css');
        $group->addJs('baz.js');

        $expected = ['foo.css' => 'foo.css', 'bar.css' => 'bar.css', 'baz.css' => 'baz.css'];
        $this->assertSame($expected, $group->getCss());

        $expected = ['foo.js' => 'foo.js', 'bar.js' => 'bar.js', 'baz.js' => 'baz.js'];
        $this->assertSame($expected, $group->getJs());
    }

    /**
     * @dataProvider dataProviderTestAddSource
     *
     * @param $source
     * @param $type
     * @param $expectedMethod
     */
    public function testAddSource($source, $type, $expectedMethod)
    {
        $group = $this->getMock('Sloths\View\Helper\Assets\Group', [$expectedMethod], [], '', false);
        $group->expects($this->once())->method($expectedMethod)->with($source);

        $group->addSource($source, $type);
    }

    public function dataProviderTestAddSource()
    {
        return [
            ['foo', 'js', 'addJs'],
            ['foo.js', null, 'addJs'],
            ['foo', 'css', 'addCss'],
            ['foo.css', null, 'addCss'],
            ['foo?foo=bar', 'css', 'addCss'],
            ['foo.css?foo=bar', null, 'addCss'],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Asset type must be js or css, foo given
     */
    public function testAddSourceShouldThrowAnExceptionWithInvalidType()
    {
        $group = $this->getMock('Sloths\View\Helper\Assets\Group', ['__construct'], [], '', false);
        $group->addSource('foo.foo');
    }

    public function testSetSources()
    {
        $group = $this->getMock('Sloths\View\Helper\Assets\Group', ['addSource'], [], '', false);

        $group->expects($this->at(0))->method('addSource')->with('foo', null)->willReturnSelf();
        $group->expects($this->at(1))->method('addSource')->with('bar', 'js')->willReturnSelf();

        $group->setSources(['foo', 'bar' => 'js']);
    }

    public function testRenderCss()
    {
        $group = $this->getMock('Sloths\View\Helper\Assets\Group', ['getCss'], [], '', false);
        $group->expects($this->once())->method('getCss')->willReturn(['foo.css' => 'foo.css', 'bar.css' => 'bar.css']);

        $expected = implode(PHP_EOL, ['<link href="foo.css" rel="stylesheet" />', '<link href="bar.css" rel="stylesheet" />']);
        $this->assertSame($expected, $group->renderCss());
    }

    public function testRenderJs()
    {
        $group = $this->getMock('Sloths\View\Helper\Assets\Group', ['getJs'], [], '', false);
        $group->expects($this->once())->method('getJs')->willReturn(['foo.js' => 'foo.js', 'bar.js' => 'bar.js']);

        $expected = implode(PHP_EOL, ['<script src="foo.js"></script>', '<script src="bar.js"></script>']);
        $this->assertSame($expected, $group->renderJs());
    }

    public function testRender()
    {
        $group = $this->getMock('Sloths\View\Helper\Assets\Group', ['renderCss', 'renderJs'], [], '', false);
        $group->expects($this->once())->method('renderCss')->willReturn('css');
        $group->expects($this->once())->method('renderJs')->willReturn('js');

        $this->assertSame('css' . PHP_EOL . 'js', $group->render());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Type must be js or css, foo given
     */
    public function testRenderShouldThrowAnExceptionWithInvalidType()
    {
        $group = $this->getMock('Sloths\View\Helper\Assets\Group', ['__construct'], [], '', false);
        $group->render('foo');
    }
}