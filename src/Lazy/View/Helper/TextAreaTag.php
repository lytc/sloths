<?php

namespace Lazy\View\Helper;

class TextAreaTag extends InputTag
{
    protected $tagName = 'textarea';

    public function processValue()
    {
        if ($this->value) {
            $this->setChildren($this->value);
        }

        return $this;
    }
}