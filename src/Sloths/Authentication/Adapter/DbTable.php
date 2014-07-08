<?php

namespace Sloths\Authentication\Adapter;

use Sloths\Authentication\Result;
use Sloths\Db\Connection;
use Sloths\Db\Sql\Select;

class DbTable extends AbstractDb
{
    const DEFAULT_TABLE_NAME = 'users';

    /**
     * @var \Sloths\Db\Connection
     */
    protected $connection;
    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var \Sloths\Db\Sql\Select
     */
    protected $sqlSelect;

    /**
     * @param Connection $connection
     * @param string $tableName
     * @param string $identityColumn
     * @param string $credentialColumn
     */
    public function __construct(Connection $connection,
                                $tableName = self::DEFAULT_TABLE_NAME,
                                $identityColumn = self::DEFAULT_IDENTITY_COLUMN,
                                $credentialColumn = self::DEFAULT_CREDENTIAL_COLUMN)
    {
        $this->connection = $connection;
        $this->tableName = $tableName;
        $this->identityColumn = $identityColumn;
        $this->credentialColumn = $credentialColumn;

        $this->sqlSelect = new Select($tableName);
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return \Sloths\Authentication\Result
     */
    public function authenticate()
    {
        $this->sqlSelect->where([$this->identityColumn => $this->getIdentity()]);
        $row = $this->connection->select($this->sqlSelect);

        if (!$row) {
            $code = Result::ERROR_IDENTITY_NOT_FOUND;
        } else {
            if ($this->verifyCredential($row[$this->credentialColumn])) {
                $code = Result::SUCCESS;
            } else {
                $code = Result::ERROR_CREDENTIAL_INVALID;
            }
        }

        $result = new Result($code, $row);
        return $result;
    }
}