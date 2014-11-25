<?php

namespace Sloths\Authentication\Adapter;

interface AdapterInterface
{
    /**
     * @param $identity
     * @return $this
     */
    public function setIdentity($identity);

    /**
     * @return mixed
     */
    public function getIdentity();

    /**
     * @param $credential
     * @return $this
     */
    public function setCredential($credential);

    /**
     * @return mixed
     */
    public function getCredential();

    /**
     * @return \Sloths\Authentication\Result
     */
    public function authenticate();
}