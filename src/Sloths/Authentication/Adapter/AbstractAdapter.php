<?php

namespace Sloths\Authentication\Adapter;

abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * @var mixed
     */
    protected $identity;

    /**
     * @var mixed
     */
    protected $credential;

    /**
     * @param $identity
     * @return $this
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @param $credential
     * @return $this
     */
    public function setCredential($credential)
    {
        return $this->credential = $credential;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCredential()
    {
        return $this->credential;
    }
}