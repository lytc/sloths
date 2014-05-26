<?php

namespace Sloths\View\Helper;

class StylesheetTag extends AssetTag
{
    const SOURCE_ATTRIBUTE = 'href';

    protected static $defaultBasePath;
    protected static $defaultDisableCachingParam;

    protected $tagName = 'link';

    protected $defaultAttributes = [
        'rel' => 'stylesheet'
    ];
}