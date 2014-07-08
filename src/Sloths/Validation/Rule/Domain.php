<?php

namespace Sloths\Validation\Rule;

class Domain extends Email
{
    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        if (!is_string($input)) {
            return false;
        }

        return parent::validate('a@' . $input);
    }
}