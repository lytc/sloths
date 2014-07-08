<?php

namespace Sloths\View\Helper;

class InputTextTag extends InputTag
{
    /**
     * @var string
     */
    protected $tagName = 'input';

    /**
     * @var array
     */
    protected $defaultAttributes = [
        'type' => 'text'
    ];
}