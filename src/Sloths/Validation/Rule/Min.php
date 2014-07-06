<?php

namespace Sloths\Validation\Rule;

class Min extends Max
{
    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        if ($this->inclusive) {
            return $input >= $this->expected;
        }

        return $input > $this->expected;
    }
}