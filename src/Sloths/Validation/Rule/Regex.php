<?php

namespace Sloths\Validation\Rule;

class Regex extends AbstractRule
{
    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        set_error_handler(function() {
            throw new \ErrorException();
        });

        $result = true;
        try {
            preg_match($input, '');
        } catch (\ErrorException $e) {
            $result = false;
        }

        restore_error_handler();

        return $result;
    }
}