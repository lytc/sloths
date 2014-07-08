<?php

namespace Sloths\Authentication\Storage;

use Sloths\Session\Session as SessionManager;

class Session implements StorageInterface
{
    const DEFAULT_NAME = '__SLOTHS_AUTH__';

    /**
     * @var mixed|\Sloths\Session\Session
     */
    protected $manager;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param SessionManager $manager
     * @param string $name
     */
    public function __construct(SessionManager $manager = null, $name = self::DEFAULT_NAME)
    {
        if (!$manager) {
            $manager = SessionManager::getInstance();
        }

        $this->manager = $manager;
        $this->name = $name;
    }

    /**
     * @return mixed|SessionManager
     */
    public function getSessionManager()
    {
        return $this->manager;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return $this->manager->has($this->name);
    }

    /**
     * @return mixed|null
     */
    public function read()
    {
        return $this->manager->get($this->name);
    }

    /**
     * @param $data
     * @return $this
     */
    public function write($data)
    {
        $this->manager->set($this->name, $data);
        return $this;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->manager->remove($this->name);
        return $this;
    }
}