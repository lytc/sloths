<?php

namespace Lazy\View\Helper;

class InputEmailTag extends InputTag
{
    protected $tagName = 'input';
    protected static $defaultAttributes = [
        'type' => 'email'
    ];
}