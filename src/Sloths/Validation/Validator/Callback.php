<?php

namespace Sloths\Validation\Validator;

class Callback extends AbstractValidator
{
    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var array
     */
    protected $args;

    /**
     * @param callable $callback
     * @param array $args
     */
    public function __construct(callable $callback, array $args = [])
    {
        $this->callback = $callback;
        $this->args = $args;
    }

    /**
     * @param mixed $input
     * @return bool
     */
    public function validate($input)
    {
        $args = [$input];
        $args = array_merge($args, $this->args);

        $result = call_user_func_array($this->callback, $args);

        if (true === $result) {
            return true;
        }

        if (is_string($result)) {
            $this->defaultMessageTemplate = $result;
        } elseif (is_array($result)) {
            $this->defaultMessageTemplate = $result[0];

            if (isset($result[1])) {
                if (!is_array($result[1])) {
                    $result[1] = [$result[1]];
                }
                $this->dataForMessage = $result[1];
            }
        }

        return false;
    }
}