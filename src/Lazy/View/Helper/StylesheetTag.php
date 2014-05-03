<?php

namespace Lazy\View\Helper;

class StylesheetTag extends AssetTag
{
    const SOURCE_ATTRIBUTE = 'href';

    protected $tagName = 'link';

    protected static $defaultAttributes = [
        'rel' => 'stylesheet'
    ];
}