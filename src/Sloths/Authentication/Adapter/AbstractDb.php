<?php

namespace Sloths\Authentication\Adapter;

abstract class AbstractDb extends AbstractAdapter
{
    const DEFAULT_IDENTITY_COLUMN = 'email';
    const DEFAULT_CREDENTIAL_COLUMN = 'password';

    /**
     * @var string
     */
    protected $identityColumn;

    /**
     * @var string
     */
    protected $credentialColumn;

    /**
     * @return string
     */
    public function getIdentityColumn()
    {
        return $this->identityColumn;
    }

    /**
     * @return string
     */
    public function getCredentialColumn()
    {
        return $this->credentialColumn;
    }
}