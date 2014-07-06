<?php

namespace Sloths\Validation\Rule;

class InstOf extends AbstractExpectedRule
{
    protected function validateExpected()
    {
        if (!is_string($this->expected)) {
            throw new \InvalidArgumentException(
                sprintf('Expects parameter 1 must be a string. %s given', gettype($this->expected))
            );
        }

        if (!class_exists($this->expected) && !interface_exists($this->expected)) {
            throw new \InvalidArgumentException(
                sprintf('Expects parameter 1 must be a valid class or interface. %s given', $this->expected)
            );
        }
    }

    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        return $input instanceof $this->expected;
    }
}