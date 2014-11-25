<?php

namespace Sloths\Session;

class Flash
{
    /**
     * @var string
     */
    protected $sessionName;

    /**
     * @var Session
     */
    protected $sessionManager;

    /**
     * @var array
     */
    protected $currentData = [];

    /**
     * @var array
     */
    protected $nextData = [];

    /**
     * @param array $data
     */
    public function __construct(array &$data)
    {
        $this->currentData = $data;
        $this->nextData = &$data;
        $this->nextData = [];
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->currentData);
    }

    /**
     * @param $name
     * @return null
     */
    public function &get($name)
    {
        $result = null;
        if (!$this->has($name)) {
            return $result;
        }

        return $this->currentData[$name];
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function set($name, $value)
    {
        $this->nextData[$name] = $value;
        return $this;
    }

    /**
     * @return $this
     */
    public function keep()
    {
        $this->nextData = $this->currentData;
        return $this;
    }

    /**
     * @return $this
     */
    public function now()
    {
        $this->currentData = $this->nextData;
        return $this;
    }
}