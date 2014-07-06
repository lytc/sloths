<?php

namespace Sloths\Validation\Rule;

class OneOf extends AbstractComposite
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

        $valid = false;

        foreach ($this->rules as $rule) {
            if ($rule->validate($input)) {
                $valid = true;
            } else {
                $this->failedRules[] = $rule;
            }
        }

        return $valid;
    }
}