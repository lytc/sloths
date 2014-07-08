<?php

namespace Sloths\Validation\Rule;

class MaxLength extends MinLength
{
    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        if (is_string($input)) {
            return strlen($input) <= $this->expected;
        } elseif (is_array($input)) {
            return count($input) <= $this->expected;
        }

        return false;
    }
}