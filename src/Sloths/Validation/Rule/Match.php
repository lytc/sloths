<?php

namespace Sloths\Validation\Rule;

class Match extends AbstractExpectedRule
{
    protected function validateExpected()
    {
        if (!is_string($this->expected)) {
            throw new \InvalidArgumentException(
                sprintf('Expects parameter 1 must be a string. %s given', gettype($this->expected))
            );
        }

        if (!(new Regex())->validate($this->expected)) {
            throw new \InvalidArgumentException(
                sprintf('Expects parameter 1 must be a valid regular expression. %s given', $this->expected)
            );
        }
    }

    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        if (!is_string($input) && !is_numeric($input)) {
            return false;
        }

        return !!preg_match($this->expected, $input);
    }
}