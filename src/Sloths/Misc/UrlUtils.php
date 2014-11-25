<?php

namespace Sloths\Misc;

class UrlUtils
{
    public static function appendParams($url, array $params)
    {
        if (!$params) {
            return $url;
        }

        $components = explode('?', $url, 2);
        $currentParams = [];

        if (isset($components[1])) {
            parse_str($components[1], $currentParams);
        }

        $params = array_replace($currentParams, $params);

        return $components[0] . '?' . http_build_query($params);
    }
}