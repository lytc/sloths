<?php

namespace Sloths\Session;

class Session implements \ArrayAccess
{
    const DEFAULT_NAMESPACE = '__SLOTHS_SESSION__';

    /**
     * @var string
     */
    protected $namespace = self::DEFAULT_NAMESPACE;

    /**
     * @var \SessionHandlerInterface
     */
    protected $saveHandler;

    /**
     * @var array
     */
    protected $container;

    /**
     * @var
     */
    protected $storage;

    /**
     * @var Session
     */
    protected static $instance;

    /**
     * @param \SessionHandlerInterface $saveHandler
     * @param array $storage
     * @return mixed
     */
    public static function getInstance(\SessionHandlerInterface $saveHandler = null, array $storage = null)
    {
        if (!static::$instance) {
            static::$instance = new self($saveHandler, $storage);
        }

        return static::$instance;
    }

    /**
     * @param \SessionHandlerInterface $saveHandler
     * @param array $storage
     */
    public function __construct(\SessionHandlerInterface $saveHandler = null, array $storage = null)
    {
        $this->saveHandler = $saveHandler;
        $this->storage = &$storage;

    }

    /**
     * @param \SessionHandlerInterface $saveHandler
     * @return $this
     */
    public function setSaveHandler(\SessionHandlerInterface $saveHandler)
    {
        $this->saveHandler = $saveHandler;
        return $this;
    }

    /**
     * @return \SessionHandlerInterface
     */
    public function getSaveHandler()
    {
        return $this->saveHandler;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return session_status() == PHP_SESSION_ACTIVE;
    }

    /**
     * @return $this
     */
    public function start()
    {
        if (!$this->isActive()) {
            if ($saveHandler = $this->getSaveHandler()) {
                session_set_save_handler($saveHandler);
            }

            session_start();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function destroy()
    {
        if ($this->isActive()) {
            session_destroy();
        }

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     * @throws \RuntimeException
     */
    public function setName($name)
    {
        if ($this->isActive()) {
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
     * @param string $id
     * @return $this
     * @throws \RuntimeException
     */
    public function setId($id)
    {
        if ($this->isActive()) {
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
     * @param bool $deleteOldSession
     * @return $this
     */
    public function regenerateId($deleteOldSession = false)
    {
        session_regenerate_id($deleteOldSession);
        return $this;
    }

    /**
     * @param string $namespace
     * @return $this
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return array
     */
    public function &getContainer()
    {
        if (null === $this->container) {
            $this->start();
            if (!$this->storage) {
                $this->storage = &$_SESSION;
            }

            if (!isset($this->storage[$this->namespace]) || !is_array($this->storage[$this->namespace])) {
                $this->storage[$this->namespace] = [];
            }
            $this->container = &$this->storage[$this->namespace];
        }

        return $this->container;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->getContainer());
    }


    /**
     * @param string $name
     * @return mixed|null
     */
    public function &get($name, $default = null)
    {
        $result = $default;

        if ($this->has($name)) {
            $result = $this->getContainer()[$name];
        }

        return $result;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function set($name, $value)
    {
        $this->getContainer()[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function remove($name)
    {
        unset($this->getContainer()[$name]);
        return $this;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->container = [];
        return $this;
    }

    /**
     * @param string $name
     * @return null
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * @param mixed $name
     * @return bool
     */
    public function offsetExists($name)
    {
        return $this->has($name);
    }

    /**
     * @param mixed $name
     * @return null
     */
    public function offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * @param mixed $name
     * @param mixed $value
     */
    public function offsetSet($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * @param mixed $name
     */
    public function offsetUnset($name)
    {
        $this->remove($name);
    }
}