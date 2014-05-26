<?php

namespace SlothsTest\View\Helper;

use Sloths\View\Helper\Tag;
use Sloths\View\View;

/**
 * @covers \Sloths\View\Helper\Tag
 */
class TagTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $tag = new Tag(new View());
        $expected = '<div></div>';

        $this->assertSame($expected, (String) $tag->tag('div'));
    }

    public function testWithAttribute()
    {
        $tag = new Tag(new View());
        $expected = '<link href="/foo" rel="stylesheet">';

        $this->assertSame($expected, (String) $tag->tag('link', ['href' => '/foo', 'rel' => 'stylesheet']));
    }

    public function testAttributes()
    {
        $tag = new Tag(new View());
        $tag->addAttributes(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $tag->getAttributes());
        $this->assertTrue($tag->hasAttribute('foo'));
        $this->assertSame('bar', $tag->getAttribute('foo'));

        $tag->removeAttribute('foo');
        $this->assertFalse($tag->hasAttribute('foo'));
        $this->assertNull($tag->getAttribute('foo'));
    }

    public function testChildren()
    {
        $tag = new Tag(new View());
        $this->assertSame([], $tag->getChildren());

        $tag->addChildren('foo');
        $this->assertSame(['foo'], $tag->getChildren());

        $tag->addChildren(['bar', 'baz']);
        $this->assertSame(['foo', 'bar', 'baz'], $tag->getChildren());

        $tag->setChildren('qux');
        $this->assertSame(['qux'], $tag->getChildren());

        $tag->tag('div');
        $this->assertSame('<div>qux</div>', (string) $tag->tag('div'));
    }
}