<?php

namespace Lazy\View\Helper;

class Escape extends AbstractHelper
{
    public function escape($str)
    {
        return htmlentities($str);
    }
}