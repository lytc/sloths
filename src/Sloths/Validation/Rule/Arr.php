<?php

namespace Sloths\Validation\Rule;

class Arr extends AbstractRule
{
    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        return is_array($input);
    }
}