<?php

namespace Sloths\View\Helper;

use Sloths\Util\UrlUtils;

class Url extends AbstractHelper
{
    /**
     * @var string
     */
    protected static $defaultUrl = '';

    /**
     * @param $url
     */
    public static function setDefaultUrl($url)
    {
        static::$defaultUrl = $url;
    }

    /**
     * @return string
     */
    public static function getDefaultUrl()
    {
        return static::$defaultUrl;
    }

    /**
     * @param string $url
     * @param array $params
     * @return string
     */
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