<?php

namespace Sloths\Validation\Rule;

class Digit extends AbstractRule
{
    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        if (is_int($input)) {
            return true;
        }

        if (!is_numeric($input)) {
            return false;
        }

        return !preg_match('/[\p{^N}]/', $input);
    }
}