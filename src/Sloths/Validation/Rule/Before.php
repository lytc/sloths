<?php

namespace Sloths\Validation\Rule;

use Sloths\Validation\ValidatableInterface;

class Before extends After
{
    /**
     * @param mixed $input
     * @return bool
     */
    public function validate($input)
    {
        $input = Date::createDateTime($input, $this->format);
        return $input && $input < $this->expectedDate;
    }
}