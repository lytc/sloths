<?php

namespace Sloths\Application\Service;

use Sloths\Encryption\Password\PasswordInterface;

class Password extends AbstractService
{
    /**
     * @var PasswordInterface
     */
    protected $adapter;

    /**
     * @param PasswordInterface $adapter
     * @return $this
     */
    public function setAdapter(PasswordInterface $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * @param bool $strict
     * @return PasswordInterface
     * @throws \DomainException
     */
    public function getAdapter($strict = true)
    {
        if (!$this->adapter && $strict) {
            throw new \DomainException('A password adapter is required');
        }

        return $this->adapter;
    }

    /**
     * @param string $password
     * @return string
     */
    public function hash($password)
    {
        return $this->getAdapter()->hash($password);
    }

    /**
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verify($password, $hash)
    {
        return $this->getAdapter()->verify($password, $hash);
    }

    /**
     * @param string $hash
     * @return bool
     */
    public function needsRehash($hash)
    {
        return $this->getAdapter()->needsRehash($hash);
    }
}