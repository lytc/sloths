<?php

namespace Sloths\Db;

use PDO;

class Connection
{
    /**
     * @var string
     */
    protected $dsn;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var array
     */
    protected $defaultOptions = [
        PDO::ATTR_CASE                  => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS          => PDO::NULL_NATURAL,
//        PDO::ATTR_STRINGIFY_FETCHES     => false,
//        PDO::ATTR_EMULATE_PREPARES      => false
    ];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string
     */
    protected $pdoClassName = 'PDO';

    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var
     */
    protected $transactionCount;

    /**
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param array $options
     */
    public function __construct($dsn, $username = null, $password = null, array $options = [])
    {
        $this->dsn      = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->options  = array_replace($this->defaultOptions, $options);
    }

    /**
     * @return array
     */
    public function getDefaultOptions()
    {
        return $this->defaultOptions;
    }

    /**
     * @param string $className
     * @return $this
     */
    public function setPdoClassName($className)
    {
        $this->pdoClassName = $className;
        return $this;
    }

    /**
     * @return PDO
     */
    public function getPdo()
    {
        if (!$this->pdo) {
            $pdoClassName = $this->pdoClassName;
            $this->pdo = new $pdoClassName($this->dsn, $this->username, $this->password, $this->options);
        }

        return $this->pdo;
    }

    /**
     * @param $sql
     * @return int
     */
    public function exec($sql)
    {
        return $this->getPdo()->exec($sql);
    }

    /**
     * @param $sql
     * @return \PDOStatement
     */
    public function query($sql)
    {
        return $this->getPdo()->query($sql);
    }

    /**
     * @return string
     */
    public function getLastInsertId()
    {
        return $this->getPdo()->lastInsertId();
    }

    /**
     * @return bool
     */
    public function beginTransaction()
    {
        if (!$this->transactionCount++) {
            return $this->getPdo()->beginTransaction();
        }

        return $this->transactionCount >= 0;
    }

    /**
     * @return bool
     */
    public function commit()
    {
        if (!--$this->transactionCount) {
            return $this->getPdo()->commit();
        }

        return $this->transactionCount >= 0;
    }

    /**
     * @return bool
     */
    public function rollBack()
    {
        if ($this->transactionCount == 1) {
            $this->transactionCount = 0;
            return $this->getPdo()->rollBack();
        }

        return --$this->transactionCount;
    }

    /**
     * @param callable $callback
     * @return mixed
     * @throws \Exception
     */
    public function transaction(callable $callback)
    {
        $this->beginTransaction();

        try {
            $result = call_user_func($callback, $this);
            $this->commit();
            return $result;
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

}