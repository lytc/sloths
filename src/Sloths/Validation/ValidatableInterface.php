<?php

namespace Sloths\Validation;

interface ValidatableInterface
{
    /**
     * @param $input
     * @return mixed
     */
    public function validate($input);
}