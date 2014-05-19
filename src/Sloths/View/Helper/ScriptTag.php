<?php

namespace Sloths\View\Helper;

class ScriptTag extends AssetTag
{
    const SOURCE_ATTRIBUTE = 'src';
    protected $tagName = 'script';

    public function scriptTag()
    {
        return call_user_func_array([$this, '__invoke'], func_get_args());
    }
}