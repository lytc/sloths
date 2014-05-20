<?php

namespace SlothsTest\View\Helper;

use Sloths\View\Helper\AssetTag;
use Sloths\View\Helper\ImageTag;
use Sloths\View\Helper\ScriptTag;
use Sloths\View\Helper\StylesheetTag;
use SlothsTest\TestCase;

class AssetTagDisableCachingTest extends TestCase
{
    public function testDefaultDisableCaching()
    {
        AssetTag::setDefaultDisableCachingParam('v1');

        $this->assertSame('v1', AssetTag::getDefaultDisableCachingParam());
        $this->assertSame('v1', ScriptTag::getDefaultDisableCachingParam());
        $this->assertSame('v1', StylesheetTag::getDefaultDisableCachingParam());
        $this->assertSame('v1', ImageTag::getDefaultDisableCachingParam());

        ScriptTag::setDefaultDisableCachingParam('v2');
        $this->assertSame('v2', ScriptTag::getDefaultDisableCachingParam());
        $this->assertSame('v1', AssetTag::getDefaultDisableCachingParam());
        $this->assertSame('v1', StylesheetTag::getDefaultDisableCachingParam());
        $this->assertSame('v1', ImageTag::getDefaultDisableCachingParam());

        StylesheetTag::setDefaultDisableCachingParam('v3');
        $this->assertSame('v3', StylesheetTag::getDefaultDisableCachingParam());
        $this->assertSame('v1', AssetTag::getDefaultDisableCachingParam());
        $this->assertSame('v2', ScriptTag::getDefaultDisableCachingParam());
        $this->assertSame('v1', ImageTag::getDefaultDisableCachingParam());

        AssetTag::setDefaultDisableCachingParam(null);
        ScriptTag::setDefaultDisableCachingParam(null);
        StylesheetTag::setDefaultDisableCachingParam(null);
        ImageTag::setDefaultDisableCachingParam(null);
    }

    public function testDefaultBasePath()
    {
        AssetTag::setDefaultBasePath('foo');

        $this->assertSame('foo', AssetTag::getDefaultBasePath());
        $this->assertSame('foo', ScriptTag::getDefaultBasePath());
        $this->assertSame('foo', StylesheetTag::getDefaultBasePath());
        $this->assertSame('foo', ImageTag::getDefaultBasePath());

        ScriptTag::setDefaultBasePath('bar');
        $this->assertSame('bar', ScriptTag::getDefaultBasePath());
        $this->assertSame('foo', AssetTag::getDefaultBasePath());
        $this->assertSame('foo', StylesheetTag::getDefaultBasePath());
        $this->assertSame('foo', ImageTag::getDefaultBasePath());

        StylesheetTag::setDefaultBasePath('baz');
        $this->assertSame('baz', StylesheetTag::getDefaultBasePath());
        $this->assertSame('foo', AssetTag::getDefaultBasePath());
        $this->assertSame('bar', ScriptTag::getDefaultBasePath());
        $this->assertSame('foo', ImageTag::getDefaultBasePath());

        AssetTag::setDefaultBasePath(null);
        ScriptTag::setDefaultBasePath(null);
        StylesheetTag::setDefaultBasePath(null);
        ImageTag::setDefaultBasePath(null);
    }
}