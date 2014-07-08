<?php

namespace Sloths\Encryption\Password;

class Password implements PasswordInterface
{
    const ALGO_DEFAULT = PASSWORD_DEFAULT;
    const ALGO_BCRYPT = PASSWORD_BCRYPT;

    /**
     * @var int
     */
    protected $algorithm;

    /**
     * @var string
     */
    protected $salt;
    /**
     * @var int
     */
    protected $cost;
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param int $algorithm
     * @param null $salt
     * @param null $cost
     */
    public function __construct($algorithm = self::ALGO_DEFAULT, $salt = null, $cost = null)
    {
        $this->algorithm = $algorithm;
        $this->salt = $salt;
        $this->cost = $cost;
    }

    /**
     * @param $algorithm
     * @return $this
     */
    public function setAlgorithm($algorithm)
    {
        $this->algorithm = $algorithm;
        return $this;
    }

    /**
     * @return int
     */
    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    /**
     * @param $salt
     * @return $this
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param $cost
     * @return $this
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
        return $this;
    }

    /**
     * @return int
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        $options = [];

        if ($salt = $this->getSalt()) {
            $options['salt'] = $salt;
        }

        if ($cost = $this->getCost()) {
            $options['cost'] = $cost;
        }

        return $options;
    }

    /**
     * @param string $password
     * @return bool|string
     */
    public function hash($password)
    {
        return password_hash($password, $this->getAlgorithm(), $this->getOptions());
    }

    /**
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verify($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * @param $hash
     * @return string
     */
    public function needsRehash($hash)
    {
        return password_needs_rehash($hash, $this->getAlgorithm(), $this->getOptions());
    }
}