<?php

namespace Lazy\View\Helper;

use Lazy\Http\Request;

class Url extends AbstractHelper
{
    /**
     * @var Request
     */
    protected static $request;

    public static function setRequest(Request $request)
    {
        self::$request = $request;
    }

    public function url(array $params) {
        $path = self::$request->getFullPathInfo();
        $paramsGet = self::$request->paramsGet();
        return $path . '?' . http_build_query(array_merge($paramsGet, $params));
    }
}