<?php

namespace Sloths\Validation\Rule;

class AllOf extends AbstractComposite
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

        $valid = true;

        foreach ($this->rules as $rule) {
            if (!$rule->validate($input)) {
                $this->failedRules[] = $rule;
                $valid = false;
            }
        }

        return $valid;
    }
}