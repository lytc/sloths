<?php

namespace Sloths\Validation\Rule;

class Positive extends AbstractRule
{
    /**
     * @param $value
     * @return bool
     */
    public function validate($value)
    {
        return $value > 0;
    }
}