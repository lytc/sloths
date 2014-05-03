<?php

namespace Lazy\View\Helper;

use Lazy\Util\UrlUtils;

class Url extends AbstractHelper
{
    protected static $defaultUrl = '';

    public static function setDefaultUrl($url)
    {
        static::$defaultUrl = $url;
    }

    public static function getDefaultUrl()
    {
        return static::$defaultUrl;
    }

    public function __invoke($url, array $params = null)
    {
        if (is_array($url)) {
            $params = $url;
            $url = static::$defaultUrl;
        }

        if ($url instanceof \Closure) {
            $url = $url();
        }

        return UrlUtils::appendParams($url, $params);
    }
}