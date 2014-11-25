<?php

namespace Sloths\Misc;

class Inflector extends \Doctrine\Common\Inflector\Inflector
{
    /**
     * @param string $str
     * @return string
     */
    public static function classify($str)
    {
        $str = strtr($str, '_-.', '   ');
        $str = ucwords($str);
        $str = str_replace(' ', '', $str);

        return $str;
    }

    /**
     * @param $str
     * @return string
     */
    public static function camelize($str)
    {
        return lcfirst(self::classify($str));
    }

    /**
     * @param string $str
     * @return string
     */
    public static function underscore($str)
    {
        $str = self::classify($str);
        $str = preg_replace('/(?<=\\w)([A-Z])/', '_$1', $str);
        $str = strtolower($str);
        return $str;
    }

    /**
     * @param string $str
     * @return string
     */
    public static function dasherize($str)
    {
        return str_replace('_', '-', self::underscore($str));
    }

    /**
     * @param string $str
     * @return string
     */
    public static function titleize($str)
    {
        return ucwords(str_replace('_', ' ', self::underscore($str)));
    }
}