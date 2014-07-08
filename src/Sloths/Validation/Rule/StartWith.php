<?php

namespace Sloths\Validation\Rule;

class StartWith extends AbstractExpectedRule
{
    /**
     * @var bool
     */
    protected $strict = true;

    /**
     * @param $expected
     * @param bool $strict
     */
    public function __construct($expected, $strict = true)
    {
        parent::__construct($expected);
        $this->strict = $strict;
    }

    /**
     * @param $value
     * @return bool
     */
    public function validate($value)
    {
        if (is_string($value) || is_numeric($value)) {
            $encoding = mb_detect_encoding($this->expected);

            if ($this->strict) {
                return 0 === mb_strpos($value, $this->expected, 0, $encoding);
            }

            return 0 === mb_stripos($value, $this->expected, 0, $encoding);
        } elseif (is_array($value)) {
            if (!$value) {
                return false;
            }

            if ($this->strict) {
                return $value[0] === $this->expected;
            }

            return $value[0] == $this->expected;
        }

        return false;
    }
}