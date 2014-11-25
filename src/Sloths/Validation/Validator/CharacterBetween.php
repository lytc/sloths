<?php

namespace Sloths\Validation\Validator;

class CharacterBetween extends AbstractValidator
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
    protected $defaultMessageTemplate = 'must be a character between :min and :max';

    /**
     * @param $min
     * @param $max
     */
    public function __construct($min, $max)
    {
        $this->min = (string) $min;
        $this->max = (string) $max;
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
        if (!is_string($input)) {
            return false;
        }

        return $input >= $this->min && $input <= $this->max;
    }
}