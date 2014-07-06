<?php

namespace Sloths\Authentication\Verifier;

class Callback implements VerifierInterface
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
     * @param $credential
     * @param $hash
     * @return mixed
     */
    public function verify($credential, $hash)
    {
        return call_user_func($this->callback, $credential, $hash);
    }
}