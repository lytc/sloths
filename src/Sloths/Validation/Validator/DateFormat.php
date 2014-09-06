<?php

namespace Sloths\Validation\Validator;

class DateFormat extends AbstractValidator
{
    /**
     * @var string
     */
    protected $defaultMessageTemplate = 'must match the format :format';

    /**
     * @var string
     */
    protected $format;

    /**
     * @param $format
     */
    public function __construct($format)
    {
        $this->format = $format;
    }

    /**
     * @return array
     */
    public function getDataForMessage()
    {
        return ['format' => $this->format];
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

        $result = date_parse_from_format($this->format, $input);

        return !$result['error_count'] && !$result['warning_count'];
    }
}