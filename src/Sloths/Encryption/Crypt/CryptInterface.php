<?php

namespace Sloths\Encryption\Crypt;

interface CryptInterface
{
    public function encrypt($data);
    public function decrypt($data);
}