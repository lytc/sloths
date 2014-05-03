<?php

namespace Lazy\Util;

class Inflector extends \Doctrine\Common\Inflector\Inflector
{
    public static function underscore($word)
    {
        return self::tableize($word);
    }
}