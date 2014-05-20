<?php

namespace Sloths\View\Helper;

class ImageTag extends AssetTag
{
    const SOURCE_ATTRIBUTE = 'src';

    protected static $defaultBasePath;
    protected static $defaultDisableCachingParam;

    protected $tagName = 'img';

}