<?php

namespace Sloths\View\Helper;

class InputEmailTag extends InputTag
{
    protected $tagName = 'input';
    protected $defaultAttributes = [
        'type' => 'email'
    ];
}