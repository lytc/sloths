<?php

namespace Sloths\Authentication;

use Sloths\Authentication\Adapter\AdapterInterface;
use Sloths\Authentication\Storage\Session;
use Sloths\Authentication\Storage\StorageInterface;

class Authenticator
{
    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @param AdapterInterface $adapter
     * @return $this
     */
    public function setAdapter(AdapterInterface $adapter = null)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * @param bool $strict
     * @return AdapterInterface
     * @throws \RuntimeException
     */
    public function getAdapter($strict = true)
    {
        if (!$this->adapter && $strict) {
            throw new \RuntimeException('An authentication adapter is required');
        }

        return $this->adapter;
    }

    /**
     * @param StorageInterface $storage
     * @return $this
     */
    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * @return Session|StorageInterface
     */
    public function getStorage()
    {
        if (!$this->storage) {
            $this->storage = new Session();
        }

        return $this->storage;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return $this->getStorage()->exists();
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->getStorage()->read();
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->getStorage()->clear();
        return $this;
    }

    /**
     * @param mixed $identity
     * @param mixed $credential
     * @return Result
     */
    public function authenticate($identity = null, $credential = null)
    {
        $adapter = $this->getAdapter();

        if ($identity) {
            $adapter->setIdentity($identity);
        }

        if ($credential) {
            $adapter->setCredential($credential);
        }

        $result = $adapter->authenticate();

        if ($result->isSuccess()) {
            $this->getStorage()->write($result->getData());
        }

        return $result;
    }


}