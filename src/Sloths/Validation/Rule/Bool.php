<?php

namespace Sloths\Validation\Rule;

class Bool extends AbstractRule
{
    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        return is_bool($input);
    }
}