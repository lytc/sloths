<?php

namespace Sloths\Validation\Rule;

class Null extends AbstractRule
{
    /**
     * @param $value
     * @return bool
     */
    public function validate($value)
    {
        return is_null($value);
    }
}