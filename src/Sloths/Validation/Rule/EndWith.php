<?php

namespace Sloths\Validation\Rule;

class EndWith extends AbstractExpectedRule
{
    /**
     * @var bool
     */
    protected $strict = true;

    /**
     * @param mixed $expected
     * @param bool $strict
     */
    public function __construct($expected, $strict = true)
    {
        parent::__construct($expected);
        $this->strict = $strict;
    }

    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        if (is_string($input) || is_numeric($input)) {
            $encoding = mb_detect_encoding($input);
            $expectedLen = mb_strlen($this->expected, $encoding);
            $haystackLen = mb_strlen($input, $encoding);


            if ($this->strict) {
                return mb_strrpos($input, $this->expected, -1, $encoding) === $haystackLen - $expectedLen;
            }

            return mb_strripos($input, $this->expected, -1, $encoding) === $haystackLen - $expectedLen;
        } elseif (is_array($input)) {
            $end = end($input);

            if ($this->strict) {
                return $end === $this->expected;
            }

            return $end == $this->expected;
        }

        return false;
    }
}