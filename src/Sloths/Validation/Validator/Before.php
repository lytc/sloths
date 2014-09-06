<?php

namespace Sloths\Validation\Validator;

class Before extends AbstractValidator
{
    /**
     * @var string
     */
    protected $defaultMessageTemplate = 'must be a date before :date';

    /**
     * @var
     */
    protected $date;

    /**
     * @var null|string
     */
    protected $format = 'm/d/Y';

    /**
     * @param $date
     * @param string $format
     */
    public function __construct($date, $format = null)
    {
        if (!$date instanceof \DateTime) {
            if ($format) {
                $date = date_create_from_format($format, $date);
            } else {
                $date = new \DateTime($date);
            }
        }

        if ($format) {
            $this->format = $format;
        }

        $this->date = $date;
    }

    /**
     * @return array
     */
    public function getDataForMessage()
    {
        return ['date' => $this->date->format($this->format)];
    }

    /**
     * @param mixed $input
     * @return bool
     */
    public function validate($input)
    {
        if (!$input instanceof \DateTime) {
            $input = date_create($input);
        }

        return $input < $this->date;
    }
}