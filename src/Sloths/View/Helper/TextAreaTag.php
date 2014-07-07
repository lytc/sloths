<?php

namespace Sloths\View\Helper;

class TextAreaTag extends InputTag
{
    /**
     * @var string
     */
    protected $tagName = 'textarea';

    /**
     * @return $this
     */
    protected function processValue()
    {
        if ($this->value) {
            $this->setChildren($this->value);
        }

        return $this;
    }
}