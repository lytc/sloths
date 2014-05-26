<?php

namespace Sloths\View\Helper;

class InputTextTag extends InputTag
{
    protected $tagName = 'input';
    protected $defaultAttributes = [
        'type' => 'text'
    ];
}