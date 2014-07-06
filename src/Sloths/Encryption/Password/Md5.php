<?php

namespace Sloths\Encryption\Password;

class Md5
{
    /**
     * @param $password
     * @return string
     */
    public function hash($password)
    {
        return md5($password);
    }

    /**
     * @param $password
     * @param $hash
     * @return bool
     */
    public function verify($password, $hash)
    {
        return $this->hash($password) === $hash;
    }
}