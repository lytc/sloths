<?php

namespace Sloths\Authentication\Verifier;
use Sloths\Encryption\Password\Md5 as Md5Encryption;

class Md5 extends Md5Encryption implements VerifierInterface
{
}