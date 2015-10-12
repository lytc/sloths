<?php

namespace Sloths\Db;

use PDO;
use Sloths\Observer\ObserverTrait;

class Connection
{
    use ObserverTrait;

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
//        PDO::ATTR_EMULATE_PREPARES      => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
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
     * @return string
     */
    public function getDsn()
    {
        return $this->dsn;
    }

    /**
     * @return string
     */
    public function getDbName()
    {
        preg_match('/dbname=([^;]+);/', $this->getDsn(), $matches);
        return $matches[1];
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
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
     * @param PDO $pdo
     * @return $this
     */
    public function setPdo(PDO $pdo)
    {
        $this->pdo = $pdo;
        return $this;
    }

    /**
     * @param $sql
     * @return int
     */
    public function exec($sql)
    {
        $this->triggerEventListener('run', [$sql]);
        $this->triggerEventListener('exec', [$sql]);

        return $this->getPdo()->exec($sql);
    }

    /**
     * @param $sql
     * @return \PDOStatement
     */
    public function query($sql)
    {
        $this->triggerEventListener('run', [$sql]);
        $this->triggerEventListener('query', [$sql]);

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