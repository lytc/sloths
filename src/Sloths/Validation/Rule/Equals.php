<?php

namespace Sloths\Validation\Rule;

class Equals extends AbstractExpectedRule
{
    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        return $this->expected == $input;
    }
}