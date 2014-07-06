<?php

namespace Sloths\Validation\Rule;

class Date extends AbstractRule
{
    /**
     * @param mixed $input
     * @param string $format
     * @return bool|\DateTime
     */
    public static function createDateTime($input, $format = null)
    {
        if ($input instanceof \DateTime) {
            return $input;
        }

        $result = false;

        if (is_string($input)) {
            $result = $format? \DateTime::createFromFormat($format, $input) : date_create("$input");
        } else if (is_int($input)) {
            $result = date_create("@$input");
        }

        if ($result) {
            $errors = \DateTime::getLastErrors();

            if ($errors['warning_count'] > 0) {
                return false;
            }

            return $result;
        }
    }

    /**
     * @var null
     */
    protected $format;

    /**
     * @param string $format
     */
    public function __construct($format = null)
    {
        if ($format) {
            $this->format = $format;
        }
    }

    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        if ($input instanceof \DateTime) {
            return true;
        }

        if (!$input || !is_string($input)) {
            return false;
        }

        $result = self::createDateTime($input, $this->format);
        return $result instanceof \DateTime;
    }
}