<?php

namespace SlothsTest\View\Helper;

use Sloths\View\Helper\SelectTag;
use Sloths\View\View;

/**
 * @covers \Sloths\View\Helper\SelectTag<extended>
 */
class SelectTagTest extends \PHPUnit_Framework_TestCase
{
    public function testWithBasicArrayOptions()
    {
        $view = new View();
        $expected = '<select name="foo"><option value="0">foo</option><option value="1">bar</option></select>';
        $this->assertSame($expected, (String) $view->selectTag('foo', ['foo', 'bar']));
    }

    public function testMultipleSelect()
    {
        $selectTag = new SelectTag(new View());
        $expected = '<select multiple="multiple" name="foo"><option value="0">foo</option><option value="1">bar</option></select>';
        $this->assertSame($expected, (String) $selectTag->selectTag('foo', ['foo', 'bar'])->setMultiple(true));

        $expected = '<select name="foo"><option value="0">foo</option><option value="1">bar</option></select>';
        $this->assertSame($expected, (String) $selectTag->selectTag('foo', ['foo', 'bar'])->setMultiple(false));
    }

    public function testWithMapValueTextProperty()
    {
        $selectTag = new SelectTag(new View());
        $expected = '<select name="foo"><option value="1">foo</option><option value="2">bar</option></select>';

        $this->assertSame($expected, (String) $selectTag->selectTag('foo', [
            ['id' => 1, 'name' => 'foo'],
            ['id' => 2, 'name' => 'bar'],
        ]));

        $this->assertSame($expected, (String) $selectTag->selectTag('foo', [
            ['foo' => 1, 'bar' => 'foo'],
            ['foo' => 2, 'bar' => 'bar'],
        ]));

        $this->assertSame($expected, (String) $selectTag->selectTag('foo', [
            ['foo' => 1, 'bar' => 'x', 'baz' => 'foo'],
            ['foo' => 2, 'bar' => 'x', 'baz' => 'bar'],
        ])->setValueProperty('foo')->setTextProperty('baz'));
    }

    public function testWithMapValueTextPropertyByCallback()
    {
        $selectTag = new SelectTag(new View());
        $expected = '<select name="foo"><option value="1">1 foo</option><option value="2">2 bar</option></select>';

        $this->assertSame($expected, (String) $selectTag->selectTag('foo', [
            ['foo' => 1, 'bar' => 'x', 'baz' => 'foo'],
            ['foo' => 2, 'bar' => 'x', 'baz' => 'bar'],
        ])->setValueProperty('foo')->setTextProperty(function($key, $value) {
                return $value['foo'] . ' ' . $value['baz'];
            }));
    }

    public function testOptionGroup()
    {
        $selectTag = new SelectTag(new View());
        $expected = '<select name="foo">' .
            '<optgroup label="foo"><option value="1">foo</option><option value="2">bar</option></optgroup>' .
            '<optgroup label="bar"><option value="3">baz</option><option value="4">qux</option></optgroup>' .
            '</select>';

        $this->assertSame($expected, (String) $selectTag->selectTag('foo', [
            'foo' => [
                ['id' => 1, 'name' => 'foo'],
                ['id' => 2, 'name' => 'bar'],
            ],
            'bar' => [
                ['id' => 3, 'name' => 'baz'],
                ['id' => 4, 'name' => 'qux'],
            ]
        ]));
    }

    public function testMixOptionAndOptionGroup()
    {
        $selectTag = new SelectTag(new View());
        $expected = '<select name="foo">' .
            '<optgroup label="foo"><option value="1">foo</option><option value="2">bar</option></optgroup>' .
            '<option value="5">wot</option>' .
            '<option value="6">wit</option>' .
            '<optgroup label="bar"><option value="3">baz</option><option value="4">qux</option></optgroup>' .
            '</select>';

        $this->assertSame($expected, (String) $selectTag->selectTag('foo', [
            'foo' => [
                ['id' => 1, 'name' => 'foo'],
                ['id' => 2, 'name' => 'bar'],
            ],
            5 => 'wot',
            ['id' => 6, 'name' => 'wit'],
             'bar' => [
                ['id' => 3, 'name' => 'baz'],
                ['id' => 4, 'name' => 'qux'],
            ]
        ]));
    }

    public function testAppendAndPrepend()
    {
        $selectTag = new SelectTag(new View());
        $expected = '<select name="foo"><option value="2">baz</option><option value="0">foo</option><option value="1">bar</option></select>';
        $this->assertSame($expected, (String) $selectTag->selectTag('foo', [0 => 'foo'])->appendOptions([1 => 'bar'])->prependOptions([2 => 'baz']));
    }
}