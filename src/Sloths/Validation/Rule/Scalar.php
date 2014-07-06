<?php

namespace Sloths\Validation\Rule;

class Scalar extends AbstractRule
{
    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        return is_scalar($input);
    }
}