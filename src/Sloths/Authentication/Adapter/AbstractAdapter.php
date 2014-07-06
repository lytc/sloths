<?php

namespace Sloths\Authentication\Adapter;

use Sloths\Authentication\Verifier\Callback;
use Sloths\Authentication\Verifier\VerifierInterface;

abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * @var
     */
    protected $identity;

    /**
     * @var
     */
    protected $credential;

    /**
     * @var
     */
    protected $credentialVerifier;

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
        $this->credential = $credential;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCredential()
    {
        return $this->credential;
    }

    /**
     * @param string|callable $verifier
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setCredentialVerifier($verifier)
    {
        $verifierNamespace = 'Sloths\Authentication\Verifier';

        if (is_string($verifier)) {
            $verifierClass = $verifierNamespace . '\\' . ucfirst($verifier);
            if (!class_exists($verifierClass)) {
                throw new \InvalidArgumentException(
                    sprintf('Verifier class not found: %s', $verifierClass)
                );
            }
            $verifier = new $verifierClass();
        } elseif (is_callable($verifier)) {
            $verifier = new Callback($verifier);
        }

        if (!$verifier instanceof VerifierInterface) {
            throw new \InvalidArgumentException(
                sprintf('Credential must be instance of %s\VerifierInterface, %s given',
                    $verifierNamespace, gettype($verifier))
            );
        }

        $this->credentialVerifier = $verifier;
        return $this;
    }

    /**
     * @return VerifierInterface
     */
    public function getCredentialVerifier()
    {
        if (!$this->credentialVerifier) {
            $this->credentialVerifier = new Callback(function ($credential, $hash) {
                return $credential == $hash;
            });
        }

        return $this->credentialVerifier;
    }

    protected function verifyCredential($hash)
    {
        return $this->getCredentialVerifier()->verify($this->credential, $hash);
    }
}