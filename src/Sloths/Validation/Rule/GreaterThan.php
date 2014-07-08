<?php

namespace Sloths\Validation\Rule;

class GreaterThan extends AbstractExpectedRule
{
    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        return $input > $this->expected;
    }
}