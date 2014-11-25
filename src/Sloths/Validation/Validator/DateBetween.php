<?php

namespace Sloths\Validation\Validator;

class DateBetween extends AbstractValidator
{
    /**
     * @var string
     */
    protected $defaultMessageTemplate = 'must be a date between :min and :max';

    /**
     * @var string
     */
    protected $format = 'm/d/Y';

    /**
     * @var \DateTime
     */
    protected $min;

    /**
     * @var \DateTime
     */
    protected $max;

    /**
     * @param string|\DateTime $min
     * @param string|\DateTime $max
     * @param string $format
     */
    public function __construct($min, $max, $format = null)
    {
        if (!$min instanceof \DateTime) {
            if ($format) {
                $min = \DateTime::createFromFormat($format, $min);
            } else {
                $min = new \DateTime($min);
            }
        }

        $this->min = $min;

        if (!$max instanceof \DateTime) {
            if ($format) {
                $max = \DateTime::createFromFormat($format, $max);
            } else {
                $max = new \DateTime($max);
            }
        }

        $this->max = $max;

        if ($format) {
            $this->format = $format;
        }
    }

    /**
     * @return array
     */
    public function getDataForMessage()
    {
        return [
            'min' => $this->min->format($this->format),
            'max' => $this->max->format($this->format),
        ];
    }

    /**
     * @param string|\DateTime $input
     * @return bool
     */
    public function validate($input)
    {
        if (is_string($input)) {
            $input = date_create($input);
        }

        if (!$input instanceof \DateTime) {
            return false;
        }

        return $input >= $this->min && $input <= $this->max;
    }
}