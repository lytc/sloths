<?php

namespace Sloths\Validation\Rule;

class Negative extends AbstractRule
{
    /**
     * @param $value
     * @return bool
     */
    public function validate($value)
    {
        return $value < 0;
    }
}