<?php

namespace Sloths\Validation\Rule;

class HasKey extends AbstractExpectedRule
{
    protected function validateExpected()
    {
        if (!is_string($this->expected) && !is_int($this->expected)) {
            throw new \InvalidArgumentException(
                sprintf('Expects parameter 1 must be a string or integer. %s given', gettype($this->expected))
            );
        }
    }
    /**
     * @param $value
     * @return bool
     */
    public function validate($value)
    {
        if (!is_array($value)) {
            return false;
        }

        return array_key_exists($this->expected, $value);
    }
}