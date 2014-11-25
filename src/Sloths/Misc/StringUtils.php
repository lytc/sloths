<?php

namespace Sloths\Misc;

class StringUtils
{
    const RANDOM_ALPHA_LOWER    = 1;
    const RANDOM_ALPHA_UPPER    = 2;
    const RANDOM_NUMERIC        = 4;
    const RANDOM_SPECIAL_CHAR   = 8;
    const RANDOM_ALPHA          = 3;
    const RANDOM_ALNUM          = 7;
    const RANDOM_ALL            = 15;

    protected static $characters = [
        self::RANDOM_ALPHA_LOWER    => 'abcdefghijklmnopqrstuvwxyz',
        self::RANDOM_ALPHA_UPPER    => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        self::RANDOM_NUMERIC        => '0123456789',
        self::RANDOM_SPECIAL_CHAR   => '`-=[]\;\',./~!@#$%^&*()+{}|:"<>?'
    ];


    /**
     * @param int $length
     * @param int $flags
     * @return string
     */
    public static function random($length, $flags = self::RANDOM_ALNUM)
    {
        $characters = '';

        foreach (self::$characters as $flag => $chars) {
            if ($flags & $flag) {
                $characters .= $chars;
            }
        }

        $characters = str_pad($characters, $length, $characters);
        return substr(str_shuffle($characters), 0, $length);
    }

    /**
     * @param string $str
     * @param array $data...
     * @return mixed
     */
    public static function format($str, $data)
    {
        if (!is_array($data)) {
            $data = func_get_args();
            array_shift($data);
        }

        $index = 0;

        return preg_replace_callback('/\?\?|\?|\:\:|\:(\b[\w_]+\b)/', function($matches) use ($data, &$index) {
            if ($matches[0] == '::') {
                return ':';
            } elseif ($matches[0] == '??') {
                return '?';
            } elseif ($matches[0] == '?') {
                $key = $index;
            } else {
                $key = $matches[1];
            }

            $value = !isset($data[$key])? '' : $data[$key];

            $index++;
            return $value;
        }, $str);
    }

    /**
     * @param string $className
     * @return string
     */
    public static function getNamespace($className)
    {
        $parts = explode('\\', $className);

        if (1 == count($parts)) {
            return;
        }

        array_pop($parts);

        return implode('\\', $parts);
    }

    /**
     * @param string $className
     * @return mixed
     */
    public static function getClassBaseName($className)
    {
        $parts = explode('\\', $className);
        return end($parts);
    }
}