<?php

namespace Sloths\View\Helper;

class StylesheetTag extends AssetTag
{
    /**
     *
     */
    const SOURCE_ATTRIBUTE = 'href';

    /**
     * @var string
     */
    protected static $defaultBasePath;
    /**
     * @var string
     */
    protected static $defaultDisableCachingParam;

    /**
     * @var string
     */
    protected $tagName = 'link';

    /**
     * @var array
     */
    protected $defaultAttributes = [
        'rel' => 'stylesheet'
    ];
}