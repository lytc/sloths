<?php

namespace Sloths\Validation\Rule;

class LeapYear extends AbstractRule
{
    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        if ((is_string($input) || is_numeric($input)) && preg_match('/^\d{4}$/', $input)) {
            $input = "$input-01-01";
        }

        $date = Date::createDateTime($input);

        if (!$date instanceof \DateTime) {
            return false;
        }

        $year = $date->format('Y');

        return $year % 4 == 0 && ($year % 100 != 0 || $year % 400 != 0);
    }
}