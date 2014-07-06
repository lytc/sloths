<?php

namespace Sloths\Validation\Rule;

class Divisible extends AbstractExpectedRule
{
    protected function validateExpected()
    {
        if (!is_numeric($this->expected)) {
            throw new \InvalidArgumentException(
                sprintf('Expects parameter 1 must be a numeric. %s given', gettype($this->expected))
            );
        }

        if ($this->expected == 0) {
            throw new \InvalidArgumentException(
                sprintf('Expects parameter 1 must be greater than 0. %s given', $this->expected)
            );
        }
    }

    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        if (!is_numeric($input)) {
            return false;
        }

        $quotient = $input / $this->expected;
        return $quotient == (int) $quotient;
    }
}