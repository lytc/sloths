<?php

namespace Sloths\Validation\Rule;

class Object extends AbstractRule
{
    /**
     * @param $value
     * @return bool
     */
    public function validate($value)
    {
        return is_object($value);
    }
}