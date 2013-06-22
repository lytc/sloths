<?php

namespace Lazy\View\Helper;

class FormatDate extends AbstractHelper
{
    protected static $format = 'd/m/y';

    public static function setDefaultFormat($format)
    {
        static::$format = $format;
    }

    public function formatDate($dateTime, $format = null)
    {
        if (!$dateTime || '0000-00-00' == $dateTime || '0000-00-00 00:00:00' == $dateTime) {
            return '';
        }

        return date($format?: static::$format, strtotime($dateTime));
    }

    public function format($format)
    {
        $this->format = $format;
        return $this;
    }
}