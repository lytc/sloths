<?php

namespace Sloths\Validation\Rule;

class NoneOf extends AbstractComposite
{
    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        $validateRequiredResult = $this->validateRequired($input);
        if (is_bool($validateRequiredResult)) {
            return $validateRequiredResult;
        }

        foreach ($this->rules as $rule) {
            if ($rule->validate($input)) {
                return false;
            }
        }

        return true;
    }
}