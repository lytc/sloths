<?php

namespace Sloths\Validation\Rule;

class Xdigit extends AbstractRule
{
    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        return ctype_xdigit($input);
    }
}