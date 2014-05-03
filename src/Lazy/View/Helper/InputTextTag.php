<?php

namespace Lazy\View\Helper;

class InputTextTag extends InputTag
{
    protected $tagName = 'input';
    protected static $defaultAttributes = [
        'type' => 'text'
    ];
}