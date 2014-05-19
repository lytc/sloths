<?php

namespace Sloths\Db;

use Sloths\Db\Sql\Delete;
use Sloths\Db\Sql\Insert;
use Sloths\Db\Sql\Select;
use Sloths\Db\Sql\Update;

class Connection
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $dbName;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var
     */
    protected $pdo;

    /**
     * @var string
     */
    protected $pdoClass = '\PDO';

    /**
     * @var
     */
    protected $transactionCount;

    /**
     * @var array
     */
    protected $pdoOptions = [
        \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
        \PDO::ATTR_STRINGIFY_FETCHES => false,
        \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
    ];

    /**
     * @param string $host
     * @param int $port
     * @param string $username
     * @param string $password
     * @param string $dbName
     */
    public function __construct($host, $port, $username, $password, $dbName)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->dbName = $dbName;
    }

    public function setPdoClass($pdoClassName)
    {
        $this->pdoClass = $pdoClassName;
        return $this;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getDbName()
    {
        return $this->dbName;
    }

    public function __sleep()
    {
        return ['host', 'port', 'username', 'password', 'dbName'];
    }

    /**
     * @param \PDO $pdo
     * @return $this
     */
    public function setPdo(\PDO $pdo)
    {
        $this->pdo = $pdo;
        return $this;
    }

    /**
     * @return \PDO
     */
    public function getPdo()
    {
        if (!$this->pdo) {
            $dns = "mysql:host={$this->host};port={$this->port};dbname={$this->dbName}";
            $this->pdo = new $this->pdoClass($dns, $this->username, $this->password, $this->pdoOptions);
        }

        return $this->pdo;
    }

    /**
     * @param mixed $value
     * @param int $type
     * @return array|int|string
     */
    public function quote($value, $type = \PDO::PARAM_STR)
    {
        $numArgs = func_num_args();

        if (is_array($value)) {
            if (2 == $numArgs) {
                foreach ($value as &$v) {
                    $v = $this->quote($v, $type);
                }
            } else {
                foreach ($value as &$v) {
                    $v = $this->quote($v);
                }
            }
            reset($value);
            return $value;
        }

        if ($numArgs == 2) {
            return $this->getPdo()->quote($value, $type);
        }

        if (is_bool($value)) {
            return $value? 1 : 0;
        }

        if (null === $value) {
            return 'NULL';
        }

        if (is_numeric($value)) {
            return $value;
        }

        if ($value instanceof Expr || $value instanceof Select) {
            return $value->toString();
        }

        return $this->getPdo()->quote($value);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function escape($value)
    {
        if (is_array($value)) {
            foreach ($value as &$val) {
                $val = $this->escape($val);
            }

            reset($value);
            return $value;
        }

        $quoted = $this->quote($value);

        if ($quoted === $value) {
            return $value;
        }

        if (is_string($value) && '\'' == $quoted[0]) {
            return substr($quoted, 1, -1);
        }

        return $quoted;
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
     * @param string $sql
     * @return int
     */
    public function exec($sql)
    {
        return $this->getPdo()->exec($sql);
    }

    /**
     * @param Insert $sql
     * @return string
     */
    public function insert(Insert $sql)
    {
        $this->exec($sql->toString());
        return $this->getPdo()->lastInsertId();
    }

    /**
     * @param Update $sql
     * @return int
     */
    public function update(Update $sql)
    {
        return $this->exec($sql->toString());
    }

    /**
     * @param Delete $sql
     * @return int
     */
    public function delete(Delete $sql)
    {
        return $this->exec($sql->toString());
    }

    /**
     * @param Select $sql
     * @return mixed
     */
    public function select(Select $sql)
    {
        return $this->query($sql->toString())->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param Select $sql
     * @param int $fetchMode
     * @return array
     */
    public function selectAll(Select $sql, $fetchMode = \PDO::FETCH_ASSOC)
    {
        return $this->query($sql->toString())->fetchAll($fetchMode);
    }

    /**
     * @param Select $sql
     * @return array
     */
    public function selectAllWithFoundRows(Select $sql)
    {
        $sql->calcFoundRows();
        return [
            'rows' => $this->selectAll($sql),
            'foundRows' => (int) $this->getPdo()->query('SELECT FOUND_ROWS()')->fetchColumn()
        ];
    }

    /**
     * @param Select $sql
     * @param int [$index=0]
     * @return string
     */
    public function selectColumn(Select $sql, $index = 0)
    {
        $stmt = $this->query($sql->toString());
        return $stmt->fetchColumn($index);
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
            if ($callback instanceof \Closure) {
                $callback = $callback->bindTo($this);
            }
            $result = call_user_func($callback, $this);
            $this->commit();
            return $result;
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }
}