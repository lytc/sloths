<?php

namespace Sloths\Authentication\Adapter;

use Sloths\Authentication\Result;

class Callback extends AbstractAdapter
{
    /**
     * @var callable
     */
    protected $callback;

    /**
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return Result
     */
    public function authenticate()
    {
        $result = call_user_func($this->callback, $this, $this->identity, $this->credential);

        if ($result instanceof Result) {
            return $result;
        }

        if (in_array($result,
            [Result::ERROR_FAILURE, Result::ERROR_IDENTITY_NOT_FOUND, Result::ERROR_CREDENTIAL_INVALID], true)) {

            return new Result($result);
        }

        return new Result(Result::SUCCESS, $result);
    }
}