<?php

namespace Sloths\Db;

use Sloths\Cache\CacheableTrait;
use Sloths\Db\Sql\Insert;
use Sloths\Db\Sql\Spec\Raw;
use Sloths\Db\Sql\SqlInterface;
use Sloths\Db\Sql\SqlReadInterface;
use Sloths\Misc\StringUtils;

class ConnectionManager
{
    use CacheableTrait;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var Connection
     */
    protected $readConnection;

    /**
     * @var Connection
     */
    protected $writeConnection;

    /**
     *
     */
    public function __construct()
    {

    }

    /**
     * @param Connection $connection
     * @return $this
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @param Connection $connection
     * @return $this
     */
    public function setReadConnection(Connection $connection)
    {
        $this->readConnection = $connection;
        return $this;
    }

    /**
     * @param Connection $connection
     * @return $this
     */
    public function setWriteConnection(Connection $connection)
    {
        $this->writeConnection = $connection;
        return $this;
    }

    /**
     * @param bool $strict
     * @return Connection
     * @throws \DomainException
     */
    public function getConnection($strict = true)
    {
        if (!$this->connection && $strict) {
            throw new \DomainException('A connection is required');
        }

        return $this->connection;
    }

    /**
     * @param bool $strict
     * @return Connection
     */
    public function getReadConnection($strict = true)
    {
        return $this->readConnection?: $this->getConnection($strict);
    }

    /**
     * @param bool $strict
     * @return Connection
     */
    public function getWriteConnection($strict = true)
    {
        return $this->writeConnection?: $this->getConnection($strict);
    }

    /**
     * @param SqlInterface $sql
     * @return \PDOStatement|int
     */
    public function run(SqlInterface $sql) {
        if ($sql instanceof SqlReadInterface) {
            return $this->getReadConnection()->query($sql->toString());
        }

        $connection = $this->getWriteConnection();
        $result = $connection->exec($sql->toString());

        if ($sql instanceof Insert) {
            return $connection->getLastInsertId();
        }

        return $result;
    }

    /**
     * @param string $expr
     * @return Raw
     */
    public static function raw($expr)
    {
        if ($expr instanceof SqlInterface) {
            return $expr;
        }

        return new Raw($expr);
    }

    /**
     * @param $input
     * @return array|string
     */
    public static function escape($input) {
        if (is_numeric($input) || is_bool($input) || is_null($input) || $input instanceof SqlInterface) {
            return $input;
        }

        if (is_array($input)) {
            return array_map('self::escape', $input);
        }

        return addcslashes($input, "\x00\n\r\\'\"\x1a");
    }

    /**
     * @param $input
     * @return array|string
     */
    public static function quote($input)
    {
        if ($input instanceof SqlInterface) {
            return $input;
        }

        $type = gettype($input);

        switch ($type) {
            case 'NULL': return 'NULL';

            case 'integer':
            case 'double':
            case 'boolean':
                return $input;

            case 'array': return array_map('self::quote', $input);
            default: return '\'' . self::escape($input) . '\'';
        }
    }

    /**
     * @param $expr
     * @param $params
     * @return mixed|string
     */
    public static function bind($expr, $params)
    {
        if (preg_match('/^[\w\.]+$/', $expr)) {
            $expr = $expr . ' = ?';
        }

        if (null === $params && preg_match('/^([\w\.]+)\s*(!)?\=\s*\?$/', $expr, $matches)) {
            $expr = $matches[1];
            return $expr . (isset($matches[2])? ' IS NOT NULL' : ' IS NULL');
        }

        if (preg_match('/^([\w\.]+)\s+(NOT\s+)?IN\s*\(\?\)$/i', $expr, $matches)) {
            $params = self::quote($params);
            if (is_array($params)) {
                $params = implode(', ', $params);
            }

            return $matches[1] . (isset($matches[2])? ' NOT' : '') . ' IN (' . $params . ')';
        }

        if (preg_match('/^([\w\.]+)\s+(NOT\s+)?LIKE\s*(%)?\?(%)?$/i', $expr, $matches)) {
            $params = self::escape($params);

            return $matches[1] .
                    ($matches[2]? ' NOT' : '')
                    . ' LIKE \''
                    . (!empty($matches[3])? '%' : '')
                    . $params
                    . (!empty($matches[4])? '%' : '')
                    . '\'';
        }

        if (!is_array($params)) {
            $params = [$params];
        }

        $params = self::quote($params);

        return StringUtils::format($expr, $params);
    }

    /**
     * @return string
     */
    public function now()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * @param string $name
     * @return Table
     */
    public function table($name)
    {
        $table = new Table($name);
        $table->setConnectionManager($this);

        if ($this->cacheManager) {
            $table->setCacheManager($this->cacheManager);
        }

        return $table;
    }
}