<?php

namespace Sloths\Observer;

class Event
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Sloths\Observer\Callback[]
     */
    protected $callbacks = [];

    /**
     * @var bool
     */
    protected $stop = false;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \Sloths\Observer\Callback[]
     */
    public function getCallbacks()
    {
        return $this->callbacks;
    }

    /**
     * @param callable|Callback $callback
     * @param int $limit
     * @return Callback
     * @throws \InvalidArgumentException
     */
    protected function processCallback($callback, $limit = -1)
    {
        if (!$callback instanceof Callback) {
            if (!is_callable($callback)) {
                throw new \InvalidArgumentException(sprintf(
                    'Callback must be instanceof \Sloths\Observer\Callback or callable. %s given', gettype($callback)
                ));
            }

            $callback = new Callback($callback, $limit);
        }

        return $callback;
    }

    /**
     * @param Callback|callable $callback
     * @param int $limit
     * @return $this
     */
    public function addCallback($callback, $limit = -1)
    {
        $this->callbacks[] = $this->processCallback($callback, $limit);
        return $this;
    }

    /**
     * @param callable|Callback $callback
     * @return $this
     */
    public function removeCallback($callback)
    {
        $callback = $this->processCallback($callback);
        if (false !== ($index = array_search($callback, $this->callbacks))) {
            if (false !== ($index = array_search($callback, $this->callbacks))) {
                unset($this->callbacks[$index]);
            }
        }

        return $this;
    }

    /**
     * @param array $args
     * @return bool|mixed|null
     */
    public function call(array $args = [])
    {
        array_unshift($args, $this);
        $result = null;

        foreach ($this->callbacks as $callback) {
            $result = $callback->call($args);

            if ($callback->isExceeded()) {
                $this->removeCallback($callback);
            }

            if ($this->stop) {
                $this->stop = false;
                break;
            }
        }

        return $result;
    }

    /**
     * @throws Stop
     */
    public function stop()
    {
        $this->stop = true;
        return $this;
    }
}