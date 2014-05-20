<?php

namespace Sloths\View\Helper;

use Sloths\Util\UrlUtils;

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

    public function __invoke($url = null, array $params = [])
    {
        if (!$url) {
            return static::$defaultUrl;
        }
        
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