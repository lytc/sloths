<?php

namespace Sloths\Util;

class StringUtils
{
    public static function random($length, $numeric = true, $upperCase = true, $specialCharacter = false)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz';

        if ($numeric) {
            $chars .= '0123456789';
        }

        if ($upperCase) {
            $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        if ($specialCharacter) {
            $chars .= '~!@#$%^&*()_+:"<>?;{}|[]\\';
        }

        return substr(str_shuffle($chars), 0, $length);
    }

    public static function getNamespace($className)
    {
        $parts = explode('\\', $className);

        if (1 == count($parts)) {
            return;
        }

        array_pop($parts);

        return implode('\\', $parts);
    }

    public static function getClassNameWithoutNamespaceName($className)
    {
        $parts = explode('\\', $className);
        return end($parts);
    }
}