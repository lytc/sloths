<?php

namespace Sloths\Validation\Rule;

class Max extends AbstractExpectedRule
{
    /**
     * @var bool
     */
    protected $inclusive = false;

    /**
     * @param $expected
     * @param bool $inclusive
     */
    public function __construct($expected, $inclusive = false)
    {
        $this->expected = $expected;
        $this->inclusive = $inclusive;

        if ($inclusive) {
            $this->messageTemplateKey = 'INCLUSIVE';
        }
    }

    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        if ($this->inclusive) {
            return $input <= $this->expected;
        }

        return $input < $this->expected;
    }
}