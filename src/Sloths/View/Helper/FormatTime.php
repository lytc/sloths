<?php

namespace Sloths\View\Helper;

class FormatTime extends FormatDateTime
{
    /**
     * @var string
     */
    protected static $defaultInputFormat = 'H:i:s';

    /**
     * @var string
     */
    protected static $defaultOutputFormat = 'h:i:s A';
}