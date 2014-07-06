<?php

namespace Sloths\Validation\Rule;

class Required extends Blank
{
    public function validate($input)
    {
        return !parent::validate($input);
    }
}