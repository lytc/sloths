<?php

namespace Sloths\Validation\Validator;

class NumberBetween extends AbstractValidator
{
    /**
     * @var int
     */
    protected $min;

    /**
     * @var int
     */
    protected $max;

    /**
     * @var string
     */
    protected $defaultMessageTemplate = 'must be a number between :min and :max';

    /**
     * @param $min
     * @param $max
     */
    public function __construct($min, $max)
    {
        $this->min = (int) $min;
        $this->max = (int) $max;
    }

    /**
     * @return array
     */
    public function getDataForMessage()
    {
        return ['min' => $this->min, 'max' => $this->max];
    }

    /**
     * @param mixed $input
     * @return bool
     */
    public function validate($input)
    {
        if (!is_numeric($input)) {
            return false;
        }

        return $input >= $this->min && $input <= $this->max;
    }
}