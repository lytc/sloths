<?php

namespace Sloths\View\Helper;

class FormatDate extends FormatDateTime
{
    /**
     * @var string
     */
    protected static $defaultInputFormat = 'Y-m-d';

    /**
     * @var string
     */
    protected static $defaultOutputFormat = 'F d, Y';
}