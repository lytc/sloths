<?php

namespace Sloths\Authentication;

use Sloths\Authentication\Adapter\AdapterInterface;
use Sloths\Authentication\Storage\Session;
use Sloths\Authentication\Storage\StorageInterface;

class Authenticator
{
    /**
     * @var Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * @var Storage\StorageInterface
     */
    protected $storage;

    /**
     * @param AdapterInterface $adapter
     * @param StorageInterface $storage
     */
    public function __construct(AdapterInterface $adapter = null, StorageInterface $storage = null)
    {
        if ($adapter) {
            $this->setAdapter($adapter);
        }

        if ($storage) {
            $this->setStorage($storage);
        }
    }

    /**
     * @param AdapterInterface $adapter
     * @return $this
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * @return AdapterInterface
     */
    public function getAdapter()
    {
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
     * @param AdapterInterface $adapter
     * @return Result
     * @throws \RuntimeException|\UnexpectedValueException
     */
    public function authenticate(AdapterInterface $adapter = null)
    {
        if ($adapter) {
            $this->setAdapter($adapter);
        }

        if (!$this->getAdapter()) {
            throw new \RuntimeException('A authentication adapter required');
        }

        $result = $this->adapter->authenticate();

        if (!$result instanceof Result) {
            throw new \UnexpectedValueException(
                sprintf('Authentication result must be a instance of %s\Result, %s given', __NAMESPACE__, gettype($result))
            );
        }

        $this->clear();

        if ($result->isSuccess()) {
            $this->getStorage()->write($result->getData());
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return $this->getStorage()->exists();
    }

    /**
     * @return mixed|null
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
}