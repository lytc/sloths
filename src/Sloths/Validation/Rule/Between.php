<?php

namespace Sloths\Validation\Rule;

class Between extends AbstractRule
{
    /**
     * @var mixed
     */
    protected $min;

    /**
     * @var mixed
     */
    protected $max;

    /**
     * @var bool
     */
    protected $inclusive = true;
    /**
     * @var AllOf
     */
    protected $rule;

    /**
     * @param mixed $min
     * @param mixed $max
     * @param bool $inclusive
     * @throws \InvalidArgumentException
     */
    public function __construct($min, $max, $inclusive = true)
    {
        if ($max < $min) {
            throw new \InvalidArgumentException(
                sprintf('Expects parameter 2 must be greater than or equal parameter 1, %s and %s given',
                    is_string($min) || is_numeric($min)? $min : gettype($min),
                    is_string($max) || is_numeric($max)? $max : gettype($max)
                )
            );
        }

        $this->min = $min;
        $this->max = $max;
        $this->inclusive = $inclusive;

        if ($inclusive) {
            $this->rule = new AllOf([new GreaterThanOrEqual($min), new LessThanOrEqual($max)]);
        } else {
            $this->rule = new AllOf([new GreaterThan($min), new LessThan($max)]);
        }
    }

    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        return $this->rule->validate($input);
    }
}