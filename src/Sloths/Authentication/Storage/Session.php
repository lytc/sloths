<?php

namespace Sloths\Authentication\Storage;

use Sloths\Session\Session as SessionManager;

class Session implements StorageInterface
{
    const DEFAULT_NAME = '__SLOTHS_AUTH__';

    /**
     * @var SessionManager
     */
    protected $sessionManager;

    /**
     * @var string
     */
    protected $sessionName;

    /**
     * @param SessionManager $manager
     * @param string $sessionName
     */
    public function __construct(SessionManager $manager = null, $sessionName = self::DEFAULT_NAME)
    {
        if (!$manager) {
            $manager = new SessionManager();
        }

        $this->sessionManager = $manager;
        $this->sessionName = $sessionName;
    }

    /**
     * @return SessionManager
     */
    public function getSessionManager()
    {
        return $this->sessionManager;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return $this->getSessionManager()->has($this->sessionName);
    }

    /**
     * @return mixed
     */
    public function read()
    {
        return $this->getSessionManager()->get($this->sessionName);
    }

    /**
     * @param $data
     * @return $this
     */
    public function write($data)
    {
        $this->getSessionManager()->set($this->sessionName, $data);
        return $this;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->getSessionManager()->remove($this->sessionName);
        return $this;
    }

}