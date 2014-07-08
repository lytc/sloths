<?php

namespace Sloths\Validation\Rule;

class String extends AbstractRule
{
    public function validate($input)
    {
        return is_string($input);
    }
}