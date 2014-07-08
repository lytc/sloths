<?php

namespace Sloths\Validation\Rule;

class Email extends AbstractRule
{
    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        return false !== filter_var($input, FILTER_VALIDATE_EMAIL);
    }
}