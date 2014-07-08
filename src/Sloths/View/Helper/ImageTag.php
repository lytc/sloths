<?php

namespace Sloths\View\Helper;

class ImageTag extends AssetTag
{
    /**
     *
     */
    const SOURCE_ATTRIBUTE = 'src';

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
    protected $tagName = 'img';

}