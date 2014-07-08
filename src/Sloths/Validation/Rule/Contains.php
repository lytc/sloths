<?php

namespace Sloths\Validation\Rule;

class Contains extends AbstractExpectedRule
{
    /**
     * @var bool
     */
    protected $strict = false;

    /**
     * @var string
     */
    protected $encoding;

    /**
     * @param $expected
     * @param bool $strict
     * @throws \InvalidArgumentException
     */
    public function __construct($expected, $strict = false)
    {
        parent::__construct($expected);

        $this->strict = $strict;

        if (is_string($expected)) {
            $this->encoding = mb_detect_encoding($this->expected);
        }
    }

    protected function validateExpected()
    {
        if (!is_string($this->expected) && !is_array($this->expected)) {
            throw new \InvalidArgumentException(
                sprintf('Expects parameter 1 must be a string or an array. %s given', gettype($this->expected))
            );
        }

    }
    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        if (is_string($this->expected)) {
            if ($this->strict) {
                return false !== mb_strpos($this->expected, $input, 0, $this->encoding);
            }
            return false !== mb_stripos($this->expected, $input, 0, $this->encoding);
        } else {
            return in_array($input, $this->expected, $this->strict);
        }
    }
}