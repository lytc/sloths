<?php

namespace Sloths\Validation\Rule;

class Upper extends AbstractRule
{
    /**
     * @param $value
     * @return bool
     */
    public function validate($value)
    {
        if (!is_string($value) && !is_numeric($value)) {
            return false;
        }

        return mb_strtoupper($value, mb_detect_encoding($value)) === $value;
    }
}