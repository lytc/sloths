<?php

namespace Sloths\Session\Adapter;
use Sloths\Session\Container;

class Native extends AbstractAdapter
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @param \SessionHandlerInterface $handler
     * @param string $namespace
     */
    public function __construct(\SessionHandlerInterface $handler = null, $namespace = '__SLOTHS__')
    {
        if ($handler) {
            session_set_save_handler($handler);
        }

        session_register_shutdown();
        $this->namespace = $namespace;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        return session_status() == PHP_SESSION_ACTIVE;
    }

    /**
     * @return $this
     */
    public function start()
    {
        if (!$this->isStarted()) {
            session_start();
        }

        return $this;
    }

    /**
     * @param string $id
     * @return $this
     * @throws \RuntimeException
     */
    public function setId($id)
    {
        if ($this->isStarted()) {
            throw new \RuntimeException('Session has already been started, use regenerateId to change the session id');
        }

        session_id($id);

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * @param string $name
     * @return $this
     * @throws \RuntimeException
     */
    public function setName($name)
    {
        if ($this->isStarted()) {
            throw new \RuntimeException('Session has already been started, cannot set session name');
        }

        session_name($name);

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return session_name();
    }

    /**
     * @param bool $deleteOldSession
     * @return $this
     */
    public function regenerateId($deleteOldSession = false)
    {
        session_regenerate_id($deleteOldSession);
        return $this;
    }

    /**
     * @return $this
     */
    public function save()
    {
        session_write_close();
        return $this;
    }

    /**
     * @return $this
     */
    public function destroy()
    {
        session_destroy();
        return $this;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        if (!$this->container) {
            $this->start();

            $namespace = $this->getNamespace();

            if (!isset($_SESSION[$namespace]) || !is_array($_SESSION[$namespace])) {
                $_SESSION[$namespace] = [];
            }

            $data = &$_SESSION[$namespace];

            $this->container = new Container($data);
        }

        return $this->container;
    }
}