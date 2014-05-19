<?php

namespace Sloths\View\Helper;

class InputPasswordTag extends InputTag
{
    protected $tagName = 'input';
    protected static $defaultAttributes = [
        'type' => 'password'
    ];
}