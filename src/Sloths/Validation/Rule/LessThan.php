<?php

namespace Sloths\Validation\Rule;

class LessThan extends AbstractExpectedRule
{
    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        return $input < $this->expected;
    }
}