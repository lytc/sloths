<?php

namespace Lazy\View\Helper;

class FormatNumber extends AbstractHelper
{
    public function formatNumber($number, $decimals = 0, $decPoint = '.', $thousandsSep = ',')
    {
        return number_format($number, $decimals, $decPoint, $thousandsSep);
    }
}