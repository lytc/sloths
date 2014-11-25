<?php

namespace Sloths\View\Helper;

class Escape
{
    /**
     * @param string $str
     * @return string
     */
    public function __invoke($str)
    {
        return htmlspecialchars($str);
    }
}