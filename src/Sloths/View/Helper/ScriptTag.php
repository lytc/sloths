<?php

namespace Sloths\View\Helper;

class ScriptTag extends AssetTag
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
    protected $tagName = 'script';

    /**
     * @return mixed
     */
    public function scriptTag()
    {
        return call_user_func_array([$this, '__invoke'], func_get_args());
    }
}