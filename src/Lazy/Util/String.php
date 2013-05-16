<?php

namespace Lazy\Util;

class String
{
    public static function camelize($ucfirst, $str = null)
    {
        if (!is_bool($ucfirst)) {
            $str = $ucfirst;
            $ucfirst = false;
        }

        $str = preg_replace_callback('/[_\-](\w{1})/', function($matches) {
            return strtoupper($matches[1]);
        }, $str);

        return $ucfirst? ucfirst($str) : $str;
    }

    public static function rand($length, $numeric = true, $upperCase = true, $specialCharacter = false)
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
}