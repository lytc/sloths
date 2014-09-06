<?php

namespace Sloths\Observer;

class Callback
{
    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var
     */
    protected $limit;

    /**
     * @param callable $callback
     * @param $limit
     */
    public function __construct(callable $callback, $limit = -1)
    {
        $this->callback = $callback;
        $this->limit = $limit;
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @return bool
     */
    public function isExceeded()
    {
        return $this->limit == 0;
    }

    /**
     * @param array $args
     * @return bool|mixed
     */
    public function call(array $args = [])
    {
        if ($this->isExceeded()) {
            return false;
        }

        $this->limit--;

        return call_user_func_array($this->getCallback(), $args);
    }
}