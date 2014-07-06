<?php

namespace Sloths\Validation\Rule;

class Int extends AbstractRule
{
    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        return is_numeric($input) && !is_float($input) && preg_match('/^\-?[\d]+$/', $input);
    }
}