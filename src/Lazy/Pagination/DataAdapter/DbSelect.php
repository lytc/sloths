<?php

namespace Lazy\Pagination\DataAdapter;

use Lazy\Db\Connection;
use Lazy\Db\Sql\Select;

class DbSelect implements DataAdapterInterface
{
    /**
     * @var \Lazy\Db\Sql\Select
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
        $stmt = $this->connection->query($this->select->toString());
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $this->count = (int) $this->connection->query("SELECT FOUND_ROWS()")->fetchColumn();

        return $rows;
    }
}