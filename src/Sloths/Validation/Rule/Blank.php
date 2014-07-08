<?php

namespace Sloths\Validation\Rule;

class Blank extends AbstractRule
{
    /**
     * @param $value
     * @return bool
     */
    public function validate($value)
    {
        if (null === $value) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) == '';
        } elseif (is_array($value)) {
            return 0 == count($value);
        }

        return false;
    }
}