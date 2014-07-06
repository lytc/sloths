<?php

namespace Sloths\Validation\Rule;

class Float extends AbstractRule
{
    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        return is_float($input) || (is_numeric($input) && preg_match('/^\-?([\d]*)\.\d+$/', $input));
    }
}