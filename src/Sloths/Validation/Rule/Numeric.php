<?php

namespace Sloths\Validation\Rule;

class Numeric extends AbstractRule
{
    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        return is_numeric($input);
    }
}