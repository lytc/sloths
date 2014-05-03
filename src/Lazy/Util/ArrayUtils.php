<?php

namespace Lazy\Util;

class ArrayUtils
{
    public static function pick(array $values, $keys, $default = null)
    {
        if (is_string($keys)) {
            $keys = preg_split('/\s+/', trim($keys));
        }

        $withDefault = func_num_args() == 3;

        $result = [];

        foreach ($keys as $key) {
            if (array_key_exists($key, $values)) {
                $result[$key] = $values[$key];
            } elseif ($withDefault) {
                $result[$key] = $default;
            }
        }

        return $result;
    }

    public static function hasOnlyInts(array $arr)
    {
        foreach ($arr as $v) {
            if (false === filter_var($v, FILTER_VALIDATE_INT)) {
                return false;
            }
        }

        return true;
    }
}