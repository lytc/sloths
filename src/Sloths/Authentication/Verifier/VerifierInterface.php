<?php

namespace Sloths\Authentication\Verifier;

interface VerifierInterface
{
    public function verify($credential, $hash);
}