<?php

namespace Lazy\Util;

class StringUtils
{
    public static function getNamespace($className)
    {
        $parts = explode('\\', $className);
        array_pop($parts);

        return implode('\\', $parts);
    }

    public static function getClassNameWithoutNamespaceName($className)
    {
        $parts = explode('\\', $className);
        return end($parts);
    }
}