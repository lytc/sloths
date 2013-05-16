<?php

namespace Lazy\View\Helper;

class StylesheetTag extends AssetTag
{
    protected $tag = 'link';
    protected $assetAttribute = 'href';
    protected $extension = 'css';
    protected $defaultAttributes = [
        'rel'   => 'stylesheet'
    ];

    public function stylesheetTag() {
        return call_user_func_array([$this, 'render'], func_get_args());
    }
}