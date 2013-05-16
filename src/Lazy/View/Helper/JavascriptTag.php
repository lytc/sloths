<?php

namespace Lazy\View\Helper;

class JavascriptTag extends AssetTag
{
    protected $tag = 'script';
    protected $assetAttribute = 'src';
    protected $extension = 'js';

    public function javascriptTag() {
        return call_user_func_array([$this, 'render'], func_get_args());
    }
}