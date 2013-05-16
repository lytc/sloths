<?php

namespace Lazy\Util;

class ArrayUtil
{
    public static function column(array $array, $valueKey, $indexKey = null)
    {
        $result = [];

        if (!$indexKey) {
            foreach ($array as $value) {
                $result[] = $value[$valueKey];
            }
        } else {
            foreach ($array as $value) {
                $result[$value[$indexKey]] = $value[$valueKey];
            }
        }

        return $result;
    }
}