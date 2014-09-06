<?php

namespace Sloths\Validation\Validator;

interface ValidatorInterface
{
    /**
     * @param mixed $input
     * @return bool
     */
    public function validate($input);

    /**
     * @return string
     */
    public function getMessageTemplate();

    /**
     * @return array
     */
    public function getDataForMessage();

    /**
     * @return string
     */
    public function getMessage();
}