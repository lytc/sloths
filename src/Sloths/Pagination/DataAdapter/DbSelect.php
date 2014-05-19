<?php

namespace Sloths\Pagination\DataAdapter;

use Sloths\Db\Connection;
use Sloths\Db\Sql\Select;

class DbSelect implements DataAdapterInterface
{
    /**
     * @var \Sloths\Db\Sql\Select
     */
    protected $select;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var int
     */
    protected $count;

    /**
     * @param Select $select
     * @param Connection $connection
     */
    public function __construct(Select $select, Connection $connection)
    {
        $select->calcFoundRows();
        $this->select = $select;
        $this->connection = $connection;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function items($limit, $offset)
    {
        $this->select->limit($limit, $offset);
        $result = $this->connection->selectAllWithFoundRows($this->select);
        $this->count = $result['foundRows'];
        return $result['rows'];
    }
}