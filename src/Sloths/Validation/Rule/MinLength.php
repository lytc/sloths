<?php

namespace Sloths\Validation\Rule;

class MinLength extends AbstractExpectedRule
{
    protected function validateExpected()
    {
        if (!(new Int())->validate($this->expected)) {
            throw new \InvalidArgumentException(
                sprintf('Expects parameter 1 must be an integer number. % given', gettype($this->expected))
            );
        }

        if ($this->expected < 0) {
            throw new \InvalidArgumentException(
                sprintf('Expects parameter 1 must be greater than or equal 0. % given', $this->expected)
            );
        }
    }

    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        if (is_string($input)) {
            return strlen($input) >= $this->expected;
        } elseif (is_array($input)) {
            return count($input) >= $this->expected;
        }

        return false;
    }
}