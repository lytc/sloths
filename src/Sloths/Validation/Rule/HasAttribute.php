<?php

namespace Sloths\Validation\Rule;

class HasAttribute extends AbstractExpectedRule
{
    protected function validateExpected()
    {
        if (!is_string($this->expected)) {
            throw new \InvalidArgumentException(
                sprintf('Expects parameter 1 must be a string. %s given', gettype($this->expected))
            );
        }
    }

    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        if (!is_object($input)) {
            return false;
        }
        return property_exists($input, $this->expected);
    }
}