<?php

namespace Sloths\Session;

use Sloths\Session\Adapter\AdapterInterface;
use Sloths\Session\Adapter\Native;

class Session
{
    /**
     * @var Adapter\AdapterInterface|Adapter\Native
     */
    protected $adapter;

    /**
     * @var Flash
     */
    protected $flash;

    /**
     * @var string
     */
    protected $flashName;

    /**
     * @param AdapterInterface $adapter
     * @param string $flashName
     */
    public function __construct(AdapterInterface $adapter = null, $flashName = '__FLASH__')
    {
        if (!$adapter) {
            $adapter = new Native();
        }

        $this->adapter = $adapter;
        $this->flashName = $flashName;
    }

    /**
     * @return AdapterInterface|Native
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return $this->getAdapter()->getContainer()->has($name);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function &get($name)
    {
        return $this->getAdapter()->getContainer()->get($name);
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function set($name, $value)
    {
        $this->getAdapter()->getContainer()->set($name, $value);
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function remove($name)
    {
        $this->getAdapter()->getContainer()->remove($name);
        return $this;
    }

    /**
     * @return Flash
     */
    public function flash()
    {
        if (!$this->flash) {
            if (!$this->has($this->flashName)) {
                $this->set($this->flashName, []);
            }

            $this->flash = new Flash($this->get($this->flashName));
        }

        return $this->flash;
    }
}